#!/usr/bin/env ruby

# Data Analysis Tool - Main entry point
# A command-line data analysis application

require_relative 'data_analyzer'
require_relative 'csv_processor'
require_relative 'statistics'
require_relative 'report_generator'
require_relative 'visualizer'

def main
  options = parse_arguments(ARGV)
  
  if options[:help] || ARGV.empty?
    show_help
    exit
  end
  
  csv_file = ARGV.find { |arg| !arg.start_with?('--') }
  
  unless csv_file
    puts "Error: Please specify a CSV file"
    show_help
    exit 1
  end
  
  unless File.exist?(csv_file)
    puts "Error: File '#{csv_file}' not found"
    exit 1
  end
  
  begin
    # Process CSV file
    processor = CSVProcessor.new(csv_file)
    data = processor.load_data
    
    # Apply filters if specified
    data = apply_filters(data, options[:filters]) if options[:filters]
    
    # Sort data if specified
    data = apply_sorting(data, options[:sort]) if options[:sort]
    
    # Analyze data
    analyzer = DataAnalyzer.new(data, processor.headers)
    
    # Generate report
    generator = ReportGenerator.new(analyzer)
    report = generator.generate_report(options)
    
    # Output results
    if options[:output]
      File.write(options[:output], report)
      puts "Report saved to #{options[:output]}"
    else
      puts report
    end
    
    # Show visualization if requested
    if options[:visualize]
      visualizer = Visualizer.new(analyzer)
      visualizer.show_charts
    end
    
  rescue => e
    puts "Error: #{e.message}"
    exit 1
  end
end

def parse_arguments(args)
  options = {
    column: nil,
    filters: [],
    sort: nil,
    output: nil,
    stats: false,
    visualize: false,
    help: false
  }
  
  i = 0
  while i < args.length
    arg = args[i]
    
    case arg
    when '--column'
      options[:column] = args[i + 1]
      i += 1
    when '--filter'
      filter_parts = args[i + 1].split(':')
      if filter_parts.length == 2
        options[:filters] << { field: filter_parts[0], value: filter_parts[1] }
      end
      i += 1
    when '--sort'
      options[:sort] = args[i + 1]
      i += 1
    when '--output'
      options[:output] = args[i + 1]
      i += 1
    when '--stats'
      options[:stats] = true
    when '--visualize'
      options[:visualize] = true
    when '--help', '-h'
      options[:help] = true
    end
    
    i += 1
  end
  
  options
end

def apply_filters(data, filters)
  filters.reduce(data) do |filtered_data, filter|
    field = filter[:field]
    value = filter[:value]
    
    filtered_data.select do |row|
      row_value = row[field]
      case row_value
      when Numeric
        row_value == value.to_f
      else
        row_value.to_s.downcase.include?(value.downcase)
      end
    end
  end
end

def apply_sorting(data, sort_field)
  data.sort_by { |row| row[sort_field] }
end

def show_help
  puts <<~HELP
    Data Analysis Tool
    ==================
    
    Usage: ruby main.rb [options] <csv_file>
    
    Options:
      --column <name>       Analyze specific column
      --filter <field:value> Filter data (field:value)
      --sort <field>        Sort by field
      --output <file>       Save results to file
      --stats               Show detailed statistics
      --visualize          Show basic visualization
      --help, -h            Show this help message
    
    Examples:
      ruby main.rb data.csv --stats
      ruby main.rb data.csv --column price --filter category:electronics
      ruby main.rb data.csv --sort price --output results.txt
    
    Supported CSV format:
    - First row should contain headers
    - Data can be numeric or text
    - Empty values are handled gracefully
  HELP
end

# Run the application
main if __FILE__ == $0
