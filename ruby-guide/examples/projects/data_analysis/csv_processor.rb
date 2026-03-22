# CSV Processor - Handles CSV file operations

require 'csv'

class CSVProcessor
  attr_reader :headers, :data, :file_path

  def initialize(file_path)
    @file_path = file_path
    @headers = []
    @data = []
  end

  def load_data
    unless File.exist?(@file_path)
      raise "File not found: #{@file_path}"
    end

    begin
      CSV.foreach(@file_path, headers: true, header_converters: :symbol) do |row|
        if @headers.empty?
          @headers = row.headers
        end
        
        # Convert row to hash with type inference
        processed_row = {}
        row.each do |key, value|
          processed_row[key] = infer_type(value)
        end
        
        @data << processed_row unless processed_row.values.all?(&:nil?)
      end
    rescue CSV::MalformedCSVError => e
      raise "CSV parsing error: #{e.message}"
    end

    puts "Loaded #{@data.length} records with #{@headers.length} columns"
    @data
  end

  def column_types
    return {} if @data.empty?

    types = {}
    @headers.each do |header|
      values = @data.map { |row| row[header] }.compact
      types[header] = infer_column_type(values)
    end
    types
  end

  def column_summary(column_name)
    values = @data.map { |row| row[column_name] }.compact
    
    {
      count: values.length,
      type: infer_column_type(values),
      null_count: @data.count { |row| row[column_name].nil? },
      unique_values: values.uniq.length
    }
  end

  def get_numeric_columns
    numeric_cols = []
    column_types.each do |column, type|
      numeric_cols << column if type == :numeric
    end
    numeric_cols
  end

  def filter_by_column(column_name, value)
    @data.select { |row| row[column_name] == value }
  end

  def sort_by_column(column_name, ascending = true)
    sorted = @data.sort_by { |row| row[column_name] || 0 }
    ascending ? sorted : sorted.reverse
  end

  private

  def infer_type(value)
    return nil if value.nil? || value.strip.empty?

    # Try to parse as numeric
    if value.match?(/^\d+$/)
      value.to_i
    elsif value.match?(/^\d*\.\d+$/)
      value.to_f
    else
      value.strip
    end
  end

  def infer_column_type(values)
    return :empty if values.empty?

    types = values.map { |v| v.class.name.downcase }
    
    if types.all? { |t| t == 'integer' || t == 'float' }
      :numeric
    elsif types.all? { |t| t == 'string' }
      :text
    else
      :mixed
    end
  end
end
