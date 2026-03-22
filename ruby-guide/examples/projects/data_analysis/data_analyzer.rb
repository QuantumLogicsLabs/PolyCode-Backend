# Data Analyzer - Core analysis logic

require_relative 'statistics'

class DataAnalyzer
  attr_reader :data, :headers

  def initialize(data, headers)
    @data = data
    @headers = headers
  end

  def analyze_column(column_name)
    values = get_numeric_values(column_name)
    return nil if values.empty?

    {
      column: column_name,
      statistics: Statistics.summary(values),
      distribution: Statistics.frequency_distribution(values)
    }
  end

  def analyze_all_numeric_columns
    numeric_cols = get_numeric_columns
    results = {}
    
    numeric_cols.each do |col|
      results[col] = analyze_column(col)
    end
    
    results
  end

  def analyze_text_column(column_name)
    values = get_text_values(column_name)
    return nil if values.empty?

    {
      column: column_name,
      count: values.length,
      unique_values: values.uniq.length,
      most_common: Statistics.frequency_distribution(values)[:distribution].first(5),
      average_length: values.map(&:length).sum.to_f / values.length
    }
  end

  def analyze_all_columns
    results = {}
    
    @headers.each do |header|
      if numeric_column?(header)
        results[header] = analyze_column(header)
      else
        results[header] = analyze_text_column(header)
      end
    end
    
    results
  end

  def correlations
    numeric_cols = get_numeric_columns
    return {} if numeric_cols.length < 2

    correlations = {}
    
    numeric_cols.each_with_index do |col1, i|
      numeric_cols.each_with_index do |col2, j|
        next if i >= j  # Avoid duplicate pairs
        
        values1 = get_numeric_values(col1)
        values2 = get_numeric_values(col2)
        
        corr = Statistics.correlation(values1, values2)
        correlations["#{col1} vs #{col2}"] = corr
      end
    end
    
    correlations
  end

  def find_outliers(column_name, method = :iqr)
    values = get_numeric_values(column_name)
    return [] if values.empty?

    case method
    when :iqr
      find_iqr_outliers(values)
    when :zscore
      find_zscore_outliers(values)
    else
      []
    end
  end

  def summary_statistics
    {
      total_records: @data.length,
      total_columns: @headers.length,
      numeric_columns: get_numeric_columns.length,
      text_columns: @headers.length - get_numeric_columns.length,
      null_counts: null_counts_by_column
    }
  end

  def top_values(column_name, limit = 10)
    values = @data.map { |row| row[column_name] }.compact
    return [] if values.empty?

    Statistics.frequency_distribution(values)[:distribution].first(limit)
  end

  def value_ranges(column_name)
    values = get_numeric_values(column_name)
    return {} if values.empty?

    {
      min: values.min,
      max: values.max,
      range: values.max - values.min,
      mean: Statistics.mean(values),
      median: Statistics.median(values)
    }
  end

  private

  def get_numeric_values(column_name)
    @data.map { |row| row[column_name] }
         .compact
         .select { |v| v.is_a?(Numeric) }
  end

  def get_text_values(column_name)
    @data.map { |row| row[column_name] }
         .compact
         .select { |v| v.is_a?(String) }
  end

  def numeric_column?(column_name)
    values = get_numeric_values(column_name)
    !values.empty?
  end

  def get_numeric_columns
    @headers.select { |header| numeric_column?(header) }
  end

  def null_counts_by_column
    null_counts = {}
    
    @headers.each do |header|
      null_count = @data.count { |row| row[header].nil? || row[header].to_s.strip.empty? }
      null_counts[header] = null_count
    end
    
    null_counts
  end

  def find_iqr_outliers(values)
    return [] if values.length < 4

    quartiles = Statistics.quartiles(values)
    q1 = quartiles[:q1]
    q3 = quartiles[:q3]
    iqr = q3 - q1
    
    lower_bound = q1 - 1.5 * iqr
    upper_bound = q3 + 1.5 * iqr
    
    values.select { |v| v < lower_bound || v > upper_bound }
  end

  def find_zscore_outliers(values, threshold = 2.0)
    return [] if values.length < 2

    mean = Statistics.mean(values)
    std_dev = Statistics.standard_deviation(values)
    
    return [] if std_dev == 0

    values.select { |v| (v - mean).abs / std_dev > threshold }
  end
end
