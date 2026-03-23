# Data Analysis in Ruby

## Overview

Data analysis is the process of inspecting, cleaning, transforming, and modeling data to discover useful information, draw conclusions, and support decision-making. Ruby provides excellent libraries and tools for comprehensive data analysis workflows.

## Data Processing Framework

### Data Pipeline Manager
```ruby
class DataPipeline
  def initialize(name)
    @name = name
    @stages = []
    @data = {}
    @metadata = {}
    @logs = []
    @status = :initialized
  end

  def add_stage(stage_name, processor, options = {})
    stage = {
      name: stage_name,
      processor: processor,
      options: options,
      status: :pending,
      input_data: nil,
      output_data: nil,
      start_time: nil,
      end_time: nil,
      error: nil
    }
    
    @stages << stage
    log("Added stage: #{stage_name}")
  end

  def load_data(source, format = :csv)
    log("Loading data from #{source}")
    
    case format
    when :csv
      @data[:raw] = load_csv_data(source)
    when :json
      @data[:raw] = load_json_data(source)
    when :excel
      @data[:raw] = load_excel_data(source)
    when :database
      @data[:raw] = load_database_data(source)
    else
      raise "Unsupported format: #{format}"
    end
    
    @metadata[:source] = source
    @metadata[:format] = format
    @metadata[:loaded_at] = Time.now
    @metadata[:record_count] = @data[:raw].length
    
    log("Loaded #{@metadata[:record_count]} records")
  end

  def execute_pipeline
    @status = :running
    log("Starting pipeline execution")
    
    current_data = @data[:raw]
    
    @stages.each_with_index do |stage, index|
      stage[:input_data] = current_data
      stage[:start_time] = Time.now
      stage[:status] = :running
      
      begin
        log("Executing stage: #{stage[:name]}")
        
        # Execute stage processor
        if stage[:processor].respond_to?(:call)
          output = stage[:processor].call(current_data, stage[:options])
        else
          output = send(stage[:processor], current_data, stage[:options])
        end
        
        stage[:output_data] = output
        stage[:status] = :completed
        stage[:end_time] = Time.now
        
        current_data = output
        @data[stage[:name]] = output
        
        log("Completed stage: #{stage[:name]} in #{stage[:end_time] - stage[:start_time]}s")
        
      rescue => e
        stage[:status] = :failed
        stage[:error] = e.message
        stage[:end_time] = Time.now
        
        @status = :failed
        log("Stage failed: #{stage[:name]} - #{e.message}")
        break
      end
    end
    
    if @status != :failed
      @status = :completed
      log("Pipeline completed successfully")
    end
    
    @data[:final] = current_data
    generate_pipeline_report
  end

  def get_stage_result(stage_name)
    stage = @stages.find { |s| s[:name] == stage_name }
    stage ? stage[:output_data] : nil
  end

  def save_results(output_path, format = :csv)
    return false unless @data[:final]
    
    log("Saving results to #{output_path}")
    
    case format
    when :csv
      save_csv_data(@data[:final], output_path)
    when :json
      save_json_data(@data[:final], output_path)
    when :excel
      save_excel_data(@data[:final], output_path)
    else
      raise "Unsupported output format: #{format}"
    end
    
    log("Results saved successfully")
  end

  def generate_pipeline_report
    report = []
    report << "Pipeline Report: #{@name}"
    report << "Status: #{@status}"
    report << "Total Stages: #{@stages.length}"
    report << "Completed Stages: #{@stages.count { |s| s[:status] == :completed }}"
    report << "Failed Stages: #{@stages.count { |s| s[:status] == :failed }}"
    report << ""
    
    total_time = 0
    @stages.each do |stage|
      if stage[:start_time] && stage[:end_time]
        duration = stage[:end_time] - stage[:start_time]
        total_time += duration
        
        report << "Stage: #{stage[:name]}"
        report << "  Status: #{stage[:status]}"
        report << "  Duration: #{duration.round(3)}s"
        report << "  Input records: #{stage[:input_data]&.length || 0}"
        report << "  Output records: #{stage[:output_data]&.length || 0}"
        report << "  Error: #{stage[:error]}" if stage[:error]
        report << ""
      end
    end
    
    report << "Total Pipeline Time: #{total_time.round(3)}s"
    report << ""
    
    if @data[:final]
      report << "Final Dataset Summary:"
      summary = analyze_dataset(@data[:final])
      summary.each { |key, value| report << "  #{key}: #{value}" }
    end
    
    report.join("\n")
  end

  private

  def log(message)
    timestamp = Time.now.strftime("%Y-%m-%d %H:%M:%S")
    log_entry = "[#{timestamp}] #{message}"
    @logs << log_entry
    puts log_entry
  end

  def load_csv_data(file_path)
    # Simplified CSV loading
    data = []
    headers = []
    
    File.readlines(file_path).each_with_index do |line, index|
      values = line.strip.split(',')
      
      if index == 0
        headers = values
      else
        record = {}
        headers.each_with_index do |header, i|
          record[header] = values[i]
        end
        data << record
      end
    end
    
    data
  end

  def load_json_data(file_path)
    # Simplified JSON loading
    JSON.parse(File.read(file_path))
  end

  def load_excel_data(file_path)
    # Simplified Excel loading (would use roo gem in practice)
    []
  end

  def load_database_data(connection_config)
    # Simplified database loading (would use sequel gem in practice)
    []
  end

  def save_csv_data(data, file_path)
    return if data.empty?
    
    headers = data.first.keys
    File.open(file_path, 'w') do |file|
      file.puts(headers.join(','))
      
      data.each do |record|
        row = headers.map { |header| record[header] }
        file.puts(row.join(','))
      end
    end
  end

  def save_json_data(data, file_path)
    File.write(file_path, JSON.pretty_generate(data))
  end

  def save_excel_data(data, file_path)
    # Simplified Excel saving (would use axlsx gem in practice)
  end

  def analyze_dataset(data)
    return {} if data.empty?
    
    {
      total_records: data.length,
      columns: data.first.keys.length,
      column_names: data.first.keys.join(', '),
      memory_usage: data.to_s.bytesize
    }
  end
end
```

### Data Cleaning Operations
```ruby
class DataCleaner
  def initialize
    @cleaning_log = []
    @statistics = {}
  end

  def remove_duplicates(data, key_columns = nil)
    original_size = data.length
    
    if key_columns
      # Remove duplicates based on specific columns
      unique_data = data.uniq { |record| 
        key_columns.map { |col| record[col] }
      }
    else
      # Remove exact duplicates
      unique_data = data.uniq
    end
    
    duplicates_removed = original_size - unique_data.length
    log_operation("remove_duplicates", "Removed #{duplicates_removed} duplicates")
    
    @statistics[:duplicates_removed] = duplicates_removed
    unique_data
  end

  def handle_missing_values(data, strategy = :remove, columns = nil)
    columns ||= data.first.keys
    original_size = data.length
    
    case strategy
    when :remove
      cleaned_data = data.reject do |record|
        columns.any? { |col| record[col].nil? || record[col].empty? }
      end
      
      records_removed = original_size - cleaned_data.length
      log_operation("handle_missing_values", "Removed #{records_removed} records with missing values")
      
    when :fill_mean
      cleaned_data = data.map do |record|
        columns.each do |col|
          if record[col].nil? || record[col].empty?
            mean_value = calculate_column_mean(data, col)
            record[col] = mean_value
          end
        end
        record
      end
      
      log_operation("handle_missing_values", "Filled missing values with mean")
      
    when :fill_median
      cleaned_data = data.map do |record|
        columns.each do |col|
          if record[col].nil? || record[col].empty?
            median_value = calculate_column_median(data, col)
            record[col] = median_value
          end
        end
        record
      end
      
      log_operation("handle_missing_values", "Filled missing values with median")
      
    when :fill_mode
      cleaned_data = data.map do |record|
        columns.each do |col|
          if record[col].nil? || record[col].empty?
            mode_value = calculate_column_mode(data, col)
            record[col] = mode_value
          end
        end
        record
      end
      
      log_operation("handle_missing_values", "Filled missing values with mode")
      
    when :fill_forward
      cleaned_data = data.map.with_index do |record, index|
        columns.each do |col|
          if record[col].nil? || record[col].empty?
            # Look forward for next non-null value
            next_value = find_next_value(data, col, index)
            record[col] = next_value
          end
        end
        record
      end
      
      log_operation("handle_missing_values", "Filled missing values with forward fill")
      
    else
      cleaned_data = data
    end
    
    cleaned_data
  end

  def standardize_text(data, columns, operations = [:lowercase, :trim])
    cleaned_data = data.map do |record|
      columns.each do |col|
        next unless record[col].is_a?(String)
        
        operations.each do |op|
          case op
          when :lowercase
            record[col] = record[col].downcase
          when :uppercase
            record[col] = record[col].upcase
          when :trim
            record[col] = record[col].strip
          when :remove_special_chars
            record[col] = record[col].gsub(/[^a-zA-Z0-9\s]/, '')
          when :normalize_whitespace
            record[col] = record[col].gsub(/\s+/, ' ').strip
          end
        end
      end
      record
    end
    
    log_operation("standardize_text", "Standardized text in columns: #{columns.join(', ')}")
    cleaned_data
  end

  def validate_data_types(data, type_schema)
    errors = []
    
    data.each_with_index do |record, index|
      type_schema.each do |column, expected_type|
        value = record[column]
        
        next if value.nil?
        
        case expected_type
        when :integer
          unless value.to_s.match?(/^\d+$/)
            errors << "Row #{index + 1}: #{column} should be integer, got '#{value}'"
          end
        when :float
          unless value.to_s.match?(/^\d*\.?\d+$/)
            errors << "Row #{index + 1}: #{column} should be float, got '#{value}'"
          end
        when :email
          unless value.match?(/\A[^@\s]+@[^@\s]+\z/)
            errors << "Row #{index + 1}: #{column} should be email, got '#{value}'"
          end
        when :date
          begin
            Date.parse(value)
          rescue ArgumentError
            errors << "Row #{index + 1}: #{column} should be date, got '#{value}'"
          end
        end
      end
    end
    
    @statistics[:validation_errors] = errors.length
    log_operation("validate_data_types", "Found #{errors.length} validation errors")
    
    {
      valid: errors.empty?,
      errors: errors,
      cleaned_data: errors.empty? ? data : fix_type_errors(data, errors, type_schema)
    }
  end

  def detect_outliers(data, columns, method = :iqr, threshold = 1.5)
    outliers = {}
    
    columns.each do |column|
      values = data.map { |record| record[column].to_f }.compact
      next if values.empty?
      
      case method
      when :iqr
        outlier_indices = detect_outliers_iqr(values, threshold)
      when :zscore
        outlier_indices = detect_outliers_zscore(values, threshold)
      when :modified_zscore
        outlier_indices = detect_outliers_modified_zscore(values, threshold)
      end
      
      outliers[column] = outlier_indices
    end
    
    total_outliers = outliers.values.sum(&:length)
    log_operation("detect_outliers", "Detected #{total_outliers} outliers")
    
    @statistics[:outliers_detected] = total_outliers
    outliers
  end

  def remove_outliers(data, outlier_indices)
    return data if outlier_indices.empty?
    
    cleaned_data = data.each_with_index.reject { |_, index| outlier_indices.include?(index) }.map(&:first)
    removed_count = data.length - cleaned_data.length
    
    log_operation("remove_outliers", "Removed #{removed_count} outlier records")
    @statistics[:outliers_removed] = removed_count
    
    cleaned_data
  end

  def normalize_data(data, columns, method = :min_max)
    normalized_data = data.map(&:dup)
    
    columns.each do |column|
      values = data.map { |record| record[column].to_f }.compact
      next if values.empty?
      
      case method
      when :min_max
        min_val = values.min
        max_val = values.max
        range = max_val - min_val
        
        normalized_data.each do |record|
          if record[column]
            record[column] = range > 0 ? (record[column].to_f - min_val) / range : 0
          end
        end
        
      when :z_score
        mean = values.sum / values.length
        std_dev = Math.sqrt(values.sum { |v| (v - mean) ** 2 } / values.length)
        
        normalized_data.each do |record|
          if record[column] && std_dev > 0
            record[column] = (record[column].to_f - mean) / std_dev
          end
        end
        
      when :robust_scaling
        median = calculate_median(values)
        mad = calculate_median_absolute_deviation(values, median)
        
        normalized_data.each do |record|
          if record[column] && mad > 0
            record[column] = (record[column].to_f - median) / mad
          end
        end
      end
    end
    
    log_operation("normalize_data", "Normalized columns: #{columns.join(', ')} using #{method}")
    normalized_data
  end

  def generate_cleaning_report
    report = []
    report << "Data Cleaning Report"
    report << "=" * 30
    report << ""
    
    @statistics.each do |operation, result|
      case operation
      when :duplicates_removed
        report << "Duplicates Removed: #{result}"
      when :validation_errors
        report << "Validation Errors: #{result}"
      when :outliers_detected
        report << "Outliers Detected: #{result}"
      when :outliers_removed
        report << "Outliers Removed: #{result}"
      end
    end
    
    report << ""
    report << "Operations Log:"
    @cleaning_log.each { |entry| report << "  #{entry}" }
    
    report.join("\n")
  end

  private

  def log_operation(operation, details)
    timestamp = Time.now.strftime("%Y-%m-%d %H:%M:%S")
    log_entry = "[#{timestamp}] #{operation}: #{details}"
    @cleaning_log << log_entry
  end

  def calculate_column_mean(data, column)
    values = data.map { |record| record[column].to_f }.compact
    values.empty? ? 0 : values.sum / values.length
  end

  def calculate_column_median(data, column)
    values = data.map { |record| record[column].to_f }.compact.sort
    return 0 if values.empty?
    
    mid = values.length / 2
    values.length.odd? ? values[mid] : (values[mid - 1] + values[mid]) / 2.0
  end

  def calculate_column_mode(data, column)
    values = data.map { |record| record[column] }.compact
    return '' if values.empty?
    
    frequency = values.group_by(&:itself).transform_values(&:count)
    frequency.max_by { |_, count| count }.first
  end

  def find_next_value(data, column, current_index)
    (current_index + 1...data.length).each do |i|
      value = data[i][column]
      return value if value && !value.empty?
    end
    nil
  end

  def fix_type_errors(data, errors, type_schema)
    # Attempt to fix type errors
    fixed_data = data.map(&:dup)
    
    errors.each do |error|
      match = error.match(/Row (\d+): (.+) should be (\w+), got '(.+)'/)
      next unless match
      
      row_index = match[1].to_i - 1
      column = match[2]
      expected_type = match[3]
      value = match[4]
      
      case expected_type
      when :integer
        fixed_data[row_index][column] = value.to_i.to_s
      when :float
        fixed_data[row_index][column] = value.to_f.to_s
      when :date
        begin
          Date.parse(value)
          fixed_data[row_index][column] = value
        rescue ArgumentError
          fixed_data[row_index][column] = nil
        end
      end
    end
    
    fixed_data
  end

  def detect_outliers_iqr(values, threshold)
    sorted = values.sort
    n = sorted.length
    
    q1_index = (n * 0.25).ceil - 1
    q3_index = (n * 0.75).ceil - 1
    
    q1 = sorted[q1_index]
    q3 = sorted[q3_index]
    iqr = q3 - q1
    
    lower_bound = q1 - threshold * iqr
    upper_bound = q3 + threshold * iqr
    
    values.each_with_index.select { |value, index| value < lower_bound || value > upper_bound }.map(&:last)
  end

  def detect_outliers_zscore(values, threshold)
    mean = values.sum / values.length
    std_dev = Math.sqrt(values.sum { |v| (v - mean) ** 2 } / values.length)
    
    values.each_with_index.select do |value, index|
      z_score = (value - mean) / std_dev
      z_score.abs > threshold
    end.map(&:last)
  end

  def detect_outliers_modified_zscore(values, threshold)
    median = calculate_median(values)
    mad = calculate_median_absolute_deviation(values, median)
    
    values.each_with_index.select do |value, index|
      modified_z_score = 0.6745 * (value - median) / mad
      modified_z_score.abs > threshold
    end.map(&:last)
  end

  def calculate_median_absolute_deviation(values, median = nil)
    median ||= calculate_median(values)
    deviations = values.map { |v| (v - median).abs }
    calculate_median(deviations)
  end

  def calculate_median(values)
    sorted = values.sort
    mid = sorted.length / 2
    sorted.length.odd? ? sorted[mid] : (sorted[mid - 1] + sorted[mid]) / 2.0
  end
end
```

## Statistical Analysis

### Advanced Statistical Methods
```ruby
class AdvancedStatistics
  def initialize
    @results = {}
  end

  def correlation_analysis(data, variables, method = :pearson)
    correlations = {}
    
    variables.combination(2) do |var1, var2|
      values1 = extract_numeric_values(data, var1)
      values2 = extract_numeric_values(data, var2)
      
      next if values1.empty? || values2.empty?
      
      case method
      when :pearson
        result = pearson_correlation(values1, values2)
      when :spearman
        result = spearman_correlation(values1, values2)
      when :kendall
        result = kendall_correlation(values1, values2)
      end
      
      correlations["#{var1}_vs_#{var2}"] = result
    end
    
    @results[:correlations] = correlations
    correlations
  end

  def regression_analysis(data, dependent_var, independent_vars)
    y = extract_numeric_values(data, dependent_var)
    x_matrix = independent_vars.map { |var| extract_numeric_values(data, var) }
    
    return {} if y.empty? || x_matrix.any?(&:empty?)
    
    # Multiple linear regression
    n = y.length
    k = independent_vars.length
    
    # Add intercept term
    x_with_intercept = x_matrix.transpose.map { |row| [1] + row }
    
    # Calculate coefficients using normal equations
    xtx = matrix_multiply(matrix_transpose(x_with_intercept), x_with_intercept)
    xty = matrix_multiply(matrix_transpose(x_with_intercept), y)
    
    coefficients = solve_linear_system(xtx, xty)
    
    # Calculate statistics
    y_pred = x_with_intercept.map { |row| row.zip(coefficients).sum { |x, c| x * c } }
    
    ss_total = y.sum { |yi| (yi - calculate_mean(y)) ** 2 }
    ss_residual = y.zip(y_pred).sum { |yi, ypi| (yi - ypi) ** 2 }
    ss_regression = ss_total - ss_residual
    
    r_squared = ss_regression / ss_total
    adjusted_r_squared = 1 - ((1 - r_squared) * (n - 1)) / (n - k - 1)
    
    # Calculate standard errors
    mse = ss_residual / (n - k - 1)
    xtx_inv = matrix_inverse(xtx)
    var_covar_matrix = matrix_scalar_multiply(xtx_inv, mse)
    
    std_errors = var_covar_matrix.diagonal.map { |var| Math.sqrt(var) }
    t_statistics = coefficients.zip(std_errors).map { |coef, se| coef / se }
    p_values = t_statistics.map { |t| calculate_t_p_value(t, n - k - 1) }
    
    # ANOVA table
    anova = {
      regression: {
        ss: ss_regression,
        df: k,
        ms: ss_regression / k,
        f: (ss_regression / k) / (ss_residual / (n - k - 1))
      },
      residual: {
        ss: ss_residual,
        df: n - k - 1,
        ms: ss_residual / (n - k - 1)
      },
      total: {
        ss: ss_total,
        df: n - 1
      }
    }
    
    result = {
      coefficients: coefficients,
      std_errors: std_errors,
      t_statistics: t_statistics,
      p_values: p_values,
      r_squared: r_squared,
      adjusted_r_squared: adjusted_r_squared,
      anova: anova,
      residuals: y.zip(y_pred).map { |yi, ypi| yi - ypi },
      predictions: y_pred
    }
    
    @results[:regression] = result
    result
  end

  def cluster_analysis(data, variables, method = :kmeans, k = 3)
    data_matrix = variables.map { |var| extract_numeric_values(data, var) }.transpose
    
    case method
    when :kmeans
      result = kmeans_clustering(data_matrix, k)
    when :hierarchical
      result = hierarchical_clustering(data_matrix)
    when :dbscan
      result = dbscan_clustering(data_matrix)
    else
      result = kmeans_clustering(data_matrix, k)
    end
    
    @results[:clustering] = result
    result
  end

  def principal_component_analysis(data, variables)
    data_matrix = variables.map { |var| extract_numeric_values(data, var) }.transpose
    
    # Standardize data
    standardized_matrix = standardize_matrix(data_matrix)
    
    # Calculate covariance matrix
    covariance_matrix = calculate_covariance_matrix(standardized_matrix)
    
    # Calculate eigenvalues and eigenvectors
    eigenvalues, eigenvectors = calculate_eigendecomposition(covariance_matrix)
    
    # Sort by eigenvalue magnitude
    sorted_indices = eigenvalues.each_with_index.sort_by { |val, _| -val }.map(&:last)
    sorted_eigenvalues = sorted_indices.map { |i| eigenvalues[i] }
    sorted_eigenvectors = sorted_indices.map { |i| eigenvectors[i] }
    
    # Calculate explained variance
    total_variance = eigenvalues.sum
    explained_variance = sorted_eigenvalues.map { |val| val / total_variance }
    cumulative_variance = explained_variance.each_with_index.map { |val, i| 
      explained_variance[0..i].sum 
    }
    
    # Transform data
    transformed_data = matrix_multiply(standardized_matrix, matrix_transpose(sorted_eigenvectors))
    
    result = {
      eigenvalues: sorted_eigenvalues,
      eigenvectors: sorted_eigenvectors,
      explained_variance: explained_variance,
      cumulative_variance: cumulative_variance,
      transformed_data: transformed_data,
      components: sorted_eigenvectors.length
    }
    
    @results[:pca] = result
    result
  end

  def time_series_analysis(data, time_column, value_column)
    times = data.map { |record| record[time_column] }
    values = data.map { |record| record[value_column].to_f }
    
    # Calculate basic statistics
    trend = calculate_trend(values)
    seasonality = detect_seasonality(values)
    autocorrelation = calculate_autocorrelation(values)
    
    # Simple moving average
    ma_5 = moving_average(values, 5)
    ma_10 = moving_average(values, 10)
    
    # Exponential smoothing
    smoothed = exponential_smoothing(values)
    
    result = {
      trend: trend,
      seasonality: seasonality,
      autocorrelation: autocorrelation,
      moving_averages: {
        ma_5: ma_5,
        ma_10: ma_10
      },
      exponential_smoothed: smoothed,
      statistics: {
        mean: calculate_mean(values),
        std_dev: calculate_std_dev(values),
        min: values.min,
        max: values.max
      }
    }
    
    @results[:time_series] = result
    result
  end

  def hypothesis_test(data, group_column, value_column, test_type = :t_test)
    groups = data.group_by { |record| record[group_column] }
    group_values = groups.transform_values { |records| records.map { |r| r[value_column].to_f } }
    
    case test_type
    when :t_test
      if group_values.length == 2
        group_names = group_values.keys
        group1, group2 = group_values.values
        
        result = independent_t_test(group1, group2)
        result[:groups] = group_names
      end
    when :anova
      result = one_way_anova(group_values.values)
      result[:groups] = group_values.keys
    when :chi_square
      result = chi_square_test(data, group_column, value_column)
    end
    
    @results[:hypothesis_test] = result
    result
  end

  def generate_analysis_report
    report = []
    report << "Advanced Statistical Analysis Report"
    report << "=" * 50
    report << ""
    
    @results.each do |analysis_type, results|
      case analysis_type
      when :correlations
        report << "Correlation Analysis:"
        results.each do |pair, result|
          report << "  #{pair}: r = #{result[:correlation].round(4)}, p = #{result[:p_value].round(4)}"
          report << "    Significant: #{result[:significant] ? 'Yes' : 'No'}"
        end
        report << ""
        
      when :regression
        report << "Regression Analysis:"
        report << "  R² = #{results[:r_squared].round(4)}"
        report << "  Adjusted R² = #{results[:adjusted_r_squared].round(4)}"
        report << "  F-statistic = #{results[:anova][:regression][:f].round(4)}"
        report << ""
        report << "  Coefficients:"
        results[:coefficients].each_with_index do |coef, i|
          se = results[:std_errors][i]
          t = results[:t_statistics][i]
          p = results[:p_values][i]
          
          report << "    β#{i} = #{coef.round(4)} (SE = #{se.round(4)}, t = #{t.round(4)}, p = #{p.round(4)})"
        end
        report << ""
        
      when :clustering
        report << "Cluster Analysis:"
        report << "  Method: #{results[:method]}"
        report << "  Number of clusters: #{results[:clusters].uniq.length}"
        report << "  Silhouette score: #{results[:silhouette_score].round(4)}"
        report << ""
        
      when :pca
        report << "Principal Component Analysis:"
        report << "  Components: #{results[:components]}"
        report << "  Total explained variance: #{results[:cumulative_variance].last.round(4)}"
        report << ""
        report << "  Component contributions:"
        results[:explained_variance].each_with_index do |var, i|
          report << "    PC#{i + 1}: #{(var * 100).round(2)}%"
        end
        report << ""
        
      when :time_series
        report << "Time Series Analysis:"
        report << "  Trend: #{results[:trend][:direction]}"
        report << "  Seasonality detected: #{results[:seasonality][:detected] ? 'Yes' : 'No'}"
        report << "  Autocorrelation (lag 1): #{results[:autocorrelation][1].round(4)}"
        report << ""
        
      when :hypothesis_test
        report << "Hypothesis Test:"
        report << "  Test type: #{results[:test_type]}"
        report << "  Groups: #{results[:groups].join(' vs ')}"
        report << "  Test statistic: #{results[:test_statistic].round(4)}"
        report << "  P-value: #{results[:p_value].round(4)}"
        report << "  Significant: #{results[:significant] ? 'Yes' : 'No'}"
        report << ""
      end
    end
    
    report.join("\n")
  end

  private

  def extract_numeric_values(data, variable)
    data.map { |record| record[variable] }.compact.map(&:to_f)
  end

  def calculate_mean(values)
    values.sum / values.length
  end

  def calculate_std_dev(values)
    mean = calculate_mean(values)
    Math.sqrt(values.sum { |v| (v - mean) ** 2 } / values.length)
  end

  def pearson_correlation(x, y)
    n = x.length
    sum_x = x.sum
    sum_y = y.sum
    sum_xy = x.zip(y).sum { |xi, yi| xi * yi }
    sum_x2 = x.sum { |xi| xi ** 2 }
    sum_y2 = y.sum { |yi| yi ** 2 }
    
    numerator = n * sum_xy - sum_x * sum_y
    denominator = Math.sqrt((n * sum_x2 - sum_x ** 2) * (n * sum_y2 - sum_y ** 2))
    
    correlation = numerator / denominator
    p_value = calculate_correlation_p_value(correlation, n)
    
    {
      correlation: correlation,
      p_value: p_value,
      significant: p_value < 0.05
    }
  end

  def spearman_correlation(x, y)
    x_ranks = x.sort.each_with_index.to_h
    y_ranks = y.sort.each_with_index.to_h
    
    x_ranked = x.map { |xi| x_ranks[xi] }
    y_ranked = y.map { |yi| y_ranks[yi] }
    
    pearson_correlation(x_ranked, y_ranked)
  end

  def kendall_correlation(x, y)
    # Simplified Kendall correlation
    n = x.length
    concordant = 0
    discordant = 0
    
    (0...n).each do |i|
      (i + 1...n).each do |j|
        x_diff = x[i] - x[j]
        y_diff = y[i] - y[j]
        
        if x_diff * y_diff > 0
          concordant += 1
        elsif x_diff * y_diff < 0
          discordant += 1
        end
      end
    end
    
    tau = (concordant - discordant).to_f / (n * (n - 1) / 2)
    
    {
      correlation: tau,
      p_value: calculate_correlation_p_value(tau, n),
      significant: false  # Simplified
    }
  end

  def matrix_multiply(a, b)
    a.map do |row|
      b.transpose.map do |col|
        row.zip(col).sum { |x, y| x * y }
      end
    end
  end

  def matrix_transpose(matrix)
    matrix.transpose
  end

  def matrix_inverse(matrix)
    # Simplified matrix inversion (for 2x2 matrices)
    return matrix_inverse_general(matrix) if matrix.length > 2
    
    a, b = matrix[0]
    c, d = matrix[1]
    
    det = a * d - b * c
    return nil if det == 0
    
    [[d / det, -b / det], [-c / det, a / det]]
  end

  def matrix_inverse_general(matrix)
    # Simplified general matrix inversion
    # In practice, use a proper linear algebra library
    matrix
  end

  def matrix_scalar_multiply(matrix, scalar)
    matrix.map { |row| row.map { |val| val * scalar } }
  end

  def solve_linear_system(a, b)
    # Simplified linear system solver
    # In practice, use a proper linear algebra library
    n = a.length
    
    # Gaussian elimination
    augmented = a.zip(b).map { |row, bi| row + [bi] }
    
    n.times do |i|
      # Pivot
      max_row = (i...n).max_by { |j| augmented[j][i].abs }
      augmented[i], augmented[max_row] = augmented[max_row], augmented[i]
      
      # Eliminate
      (i + 1...n).each do |j|
        factor = augmented[j][i] / augmented[i][i]
        (i...n + 1).each do |k|
          augmented[j][k] -= factor * augmented[i][k]
        end
      end
    end
    
    # Back substitution
    solution = Array.new(n, 0)
    (n - 1).downto(0) do |i|
      solution[i] = augmented[i][n]
      (i + 1...n).each do |j|
        solution[i] -= augmented[i][j] * solution[j]
      end
      solution[i] /= augmented[i][i]
    end
    
    solution
  end

  def kmeans_clustering(data, k)
    n = data.length
    dimensions = data.first.length
    
    # Initialize centroids randomly
    centroids = Array.new(k) { data.sample }
    
    iterations = 100
    clusters = Array.new(n)
    
    iterations.times do
      # Assign points to clusters
      data.each_with_index do |point, i|
        distances = centroids.map { |centroid| euclidean_distance(point, centroid) }
        clusters[i] = distances.index(distances.min)
      end
      
      # Update centroids
      new_centroids = Array.new(k) do |cluster_idx|
        cluster_points = data.each_with_index.select { |_, i| clusters[i] == cluster_idx }.map(&:first)
        
        if cluster_points.empty?
          centroids[cluster_idx]
        else
          dimensions.times.map do |dim|
            cluster_points.map { |point| point[dim] }.sum / cluster_points.length
          end
        end
      end
      
      # Check convergence
      break if centroids.zip(new_centroids).all? { |old, new| euclidean_distance(old, new) < 1e-6 }
      centroids = new_centroids
    end
    
    # Calculate silhouette score
    silhouette_score = calculate_silhouette_score(data, clusters, centroids)
    
    {
      method: :kmeans,
      clusters: clusters,
      centroids: centroids,
      silhouette_score: silhouette_score,
      iterations: iterations
    }
  end

  def hierarchical_clustering(data)
    # Simplified hierarchical clustering
    n = data.length
    clusters = (0...n).map { |i| [i] }
    
    while clusters.length > 1
      # Find closest clusters
      min_distance = Float::INFINITY
      merge_indices = [0, 1]
      
      clusters.each_with_index do |cluster1, i|
        clusters.each_with_index do |cluster2, j|
          next if i >= j
          
          distance = cluster_distance(data, cluster1, cluster2)
          if distance < min_distance
            min_distance = distance
            merge_indices = [i, j]
          end
        end
      end
      
      # Merge clusters
      merged_cluster = clusters[merge_indices[0]] + clusters[merge_indices[1]]
      clusters.delete_at(merge_indices[1])
      clusters[merge_indices[0]] = merged_cluster
    end
    
    final_clusters = clusters.first
    
    {
      method: :hierarchical,
      clusters: final_clusters,
      dendrogram: []  # Simplified
    }
  end

  def dbscan_clustering(data, eps = 0.5, min_points = 5)
    n = data.length
    visited = Array.new(n, false)
    clusters = Array.new(n, -1)
    cluster_id = 0
    
    (0...n).each do |i|
      next if visited[i]
      
      visited[i] = true
      neighbors = find_neighbors(data, i, eps)
      
      if neighbors.length >= min_points
        clusters[i] = cluster_id
        
        # Expand cluster
        neighbors.each do |neighbor|
          next if visited[neighbor]
          visited[neighbor] = true
          
          neighbor_neighbors = find_neighbors(data, neighbor, eps)
          
          if neighbor_neighbors.length >= min_points
            neighbors += neighbor_neighbors
          end
          
          if clusters[neighbor] == -1
            clusters[neighbor] = cluster_id
          end
        end
        
        cluster_id += 1
      end
    end
    
    {
      method: :dbscan,
      clusters: clusters,
      noise_points: clusters.count(-1),
      cluster_count: clusters.max + 1
    }
  end

  def euclidean_distance(point1, point2)
    Math.sqrt(point1.zip(point2).sum { |x, y| (x - y) ** 2 })
  end

  def cluster_distance(data, cluster1, cluster2)
    # Single linkage clustering
    min_distance = Float::INFINITY
    
    cluster1.each do |i|
      cluster2.each do |j|
        distance = euclidean_distance(data[i], data[j])
        min_distance = [min_distance, distance].min
      end
    end
    
    min_distance
  end

  def find_neighbors(data, point_index, eps)
    neighbors = []
    
    data.each_with_index do |point, i|
      next if i == point_index
      
      if euclidean_distance(data[point_index], point) <= eps
        neighbors << i
      end
    end
    
    neighbors
  end

  def calculate_silhouette_score(data, clusters, centroids)
    return 0 if clusters.uniq.length <= 1
    
    total_score = 0
    count = 0
    
    data.each_with_index do |point, i|
      cluster = clusters[i]
      
      # Calculate a(i): average distance to points in same cluster
      same_cluster_points = data.each_with_index.select { |_, j| clusters[j] == cluster && j != i }.map(&:first)
      
      next if same_cluster_points.empty?
      
      a = same_cluster_points.sum { |other_point| euclidean_distance(point, other_point) } / same_cluster_points.length
      
      # Calculate b(i): minimum average distance to points in other clusters
      other_clusters = clusters.uniq - [cluster]
      b_values = []
      
      other_clusters.each do |other_cluster|
        other_cluster_points = data.each_with_index.select { |_, j| clusters[j] == other_cluster }.map(&:first)
        
        if other_cluster_points.any?
          avg_distance = other_cluster_points.sum { |other_point| euclidean_distance(point, other_point) } / other_cluster_points.length
          b_values << avg_distance
        end
      end
      
      next if b_values.empty?
      
      b = b_values.min
      
      # Calculate silhouette score for this point
      silhouette = (b - a) / [a, b].max
      total_score += silhouette
      count += 1
    end
    
    count > 0 ? total_score / count : 0
  end

  def standardize_matrix(matrix)
    n = matrix.length
    m = matrix.first.length
    
    standardized = Array.new(n) { Array.new(m) }
    
    (0...m).each do |j|
      column = matrix.map { |row| row[j] }
      mean = column.sum / column.length
      std_dev = Math.sqrt(column.sum { |val| (val - mean) ** 2 } / column.length)
      
      (0...n).each do |i|
        standardized[i][j] = std_dev > 0 ? (matrix[i][j] - mean) / std_dev : 0
      end
    end
    
    standardized
  end

  def calculate_covariance_matrix(matrix)
    n = matrix.length
    m = matrix.first.length
    
    # Calculate means
    means = Array.new(m) { |j| matrix.sum { |row| row[j] } / n }
    
    # Calculate covariance matrix
    covariance = Array.new(m) { Array.new(m, 0) }
    
    (0...m).each do |i|
      (0...m).each do |j|
        covariance[i][j] = matrix.sum { |row| (row[i] - means[i]) * (row[j] - means[j]) } / (n - 1)
      end
    end
    
    covariance
  end

  def calculate_eigendecomposition(matrix)
    # Simplified eigenvalue decomposition
    # In practice, use a proper linear algebra library
    m = matrix.length
    
    # Power iteration for largest eigenvalue
    eigenvector = Array.new(m) { rand }
    eigenvalue = 0
    
    100.times do
      new_vector = matrix.map { |row| row.zip(eigenvector).sum { |x, y| x * y } }
      norm = Math.sqrt(new_vector.sum { |x| x ** 2 })
      eigenvector = new_vector.map { |x| x / norm }
      eigenvalue = matrix.map { |row| row.zip(eigenvector).sum { |x, y| x * y } }.zip(eigenvector).sum { |x, y| x * y }
    end
    
    eigenvalues = [eigenvalue] + Array.new(m - 1, 0)
    eigenvectors = [eigenvector] + Array.new(m - 1) { Array.new(m, 0) }
    
    [eigenvalues, eigenvectors]
  end

  def calculate_trend(values)
    n = values.length
    x = (0...n).to_a
    
    sum_x = x.sum
    sum_y = values.sum
    sum_xy = x.zip(values).sum { |xi, yi| xi * yi }
    sum_x2 = x.sum { |xi| xi ** 2 }
    
    slope = (n * sum_xy - sum_x * sum_y) / (n * sum_x2 - sum_x ** 2)
    
    {
      slope: slope,
      direction: slope > 0 ? :increasing : slope < 0 ? :decreasing : :stable
    }
  end

  def detect_seasonality(values)
    # Simple seasonality detection using autocorrelation
    max_lag = values.length / 4
    autocorr = calculate_autocorrelation(values, max_lag)
    
    # Find significant peaks
    threshold = 0.3
    seasonal_periods = []
    
    (1...max_lag).each do |lag|
      if autocorr[lag].abs > threshold
        seasonal_periods << lag
      end
    end
    
    {
      detected: seasonal_periods.any?,
      periods: seasonal_periods
    }
  end

  def calculate_autocorrelation(values, max_lag = 20)
    n = values.length
    mean = values.sum / n
    
    autocorr = [1.0]  # Lag 0
    
    (1...[max_lag, n - 1].min).each do |lag|
      numerator = (0...(n - lag)).sum { |i| (values[i] - mean) * (values[i + lag] - mean) }
      denominator = (0...n).sum { |i| (values[i] - mean) ** 2 }
      
      autocorr << numerator / denominator
    end
    
    autocorr
  end

  def moving_average(values, window)
    ma = []
    
    (window - 1...values.length).each do |i|
      window_values = values[(i - window + 1)..i]
      ma << window_values.sum / window
    end
    
    ma
  end

  def exponential_smoothing(values, alpha = 0.3)
    smoothed = [values.first]
    
    (1...values.length).each do |i|
      smoothed_value = alpha * values[i] + (1 - alpha) * smoothed[i - 1]
      smoothed << smoothed_value
    end
    
    smoothed
  end

  def independent_t_test(group1, group2)
    n1 = group1.length
    n2 = group2.length
    mean1 = group1.sum / n1
    mean2 = group2.sum / n2
    var1 = group1.sum { |x| (x - mean1) ** 2 } / (n1 - 1)
    var2 = group2.sum { |x| (x - mean2) ** 2 } / (n2 - 1)
    
    pooled_var = ((n1 - 1) * var1 + (n2 - 1) * var2) / (n1 + n2 - 2)
    se = Math.sqrt(pooled_var * (1.0/n1 + 1.0/n2))
    
    t_statistic = (mean1 - mean2) / se
    df = n1 + n2 - 2
    p_value = calculate_t_p_value(t_statistic, df)
    
    {
      test_type: :independent_t_test,
      test_statistic: t_statistic,
      p_value: p_value,
      df: df,
      mean1: mean1,
      mean2: mean2,
      significant: p_value < 0.05
    }
  end

  def one_way_anova(groups)
    all_values = groups.flatten
    grand_mean = all_values.sum / all_values.length
    
    # Between-group variance
    group_means = groups.map { |group| group.sum / group.length }
    group_sizes = groups.map(&:length)
    
    ss_between = group_sizes.zip(group_means).sum { |size, mean| size * (mean - grand_mean) ** 2 }
    df_between = groups.length - 1
    ms_between = ss_between / df_between
    
    # Within-group variance
    ss_within = groups.sum do |group|
      group_mean = group.sum / group.length
      group.sum { |value| (value - group_mean) ** 2 }
    end
    df_within = all_values.length - groups.length
    ms_within = ss_within / df_within
    
    # F-statistic
    f_statistic = ms_between / ms_within
    p_value = calculate_f_p_value(f_statistic, df_between, df_within)
    
    {
      test_type: :one_way_anova,
      test_statistic: f_statistic,
      p_value: p_value,
      df_between: df_between,
      df_within: df_within,
      significant: p_value < 0.05
    }
  end

  def chi_square_test(data, group_column, value_column)
    # Simplified chi-square test for independence
    contingency_table = build_contingency_table(data, group_column, value_column)
    
    chi_square = 0
    total = contingency_table.values.sum
    
    contingency_table.each do |(group, value), observed|
      row_total = contingency_table.select { |(g, _), _| g == group }.values.sum
      col_total = contingency_table.select { |(_, v), _| v == value }.values.sum
      expected = (row_total * col_total).to_f / total
      
      chi_square += ((observed - expected) ** 2) / expected
    end
    
    df = (contingency_table.keys.map(&:first).uniq.length - 1) * 
          (contingency_table.keys.map(&:last).uniq.length - 1)
    
    p_value = calculate_chi_square_p_value(chi_square, df)
    
    {
      test_type: :chi_square,
      test_statistic: chi_square,
      p_value: p_value,
      df: df,
      significant: p_value < 0.05
    }
  end

  def build_contingency_table(data, group_column, value_column)
    table = Hash.new(0)
    
    data.each do |record|
      group = record[group_column]
      value = record[value_column]
      table[[group, value]] += 1
    end
    
    table
  end

  def calculate_t_p_value(t, df)
    # Simplified t-distribution p-value
    calculate_p_value(t, df)
  end

  def calculate_f_p_value(f, df1, df2)
    # Simplified F-distribution p-value
    calculate_p_value(f, df1 + df2)
  end

  def calculate_chi_square_p_value(chi_square, df)
    # Simplified chi-square p-value
    calculate_p_value(chi_square, df)
  end

  def calculate_correlation_p_value(correlation, n)
    # Simplified correlation p-value
    t = correlation * Math.sqrt((n - 2) / (1 - correlation ** 2))
    calculate_t_p_value(t, n - 2)
  end

  def calculate_p_value(statistic, df, test_type = :two_tailed)
    # Simplified p-value calculation
    case test_type
    when :two_tailed
      2 * (1 - normal_cdf(Math.abs(statistic)))
    else
      1 - normal_cdf(statistic)
    end
  end

  def normal_cdf(x)
    # Simplified normal CDF approximation
    0.5 * (1 + Math.erf(x / Math.sqrt(2)))
  end
end
```

## Best Practices

1. **Data Quality**: Always validate and clean data before analysis
2. **Statistical Assumptions**: Check assumptions before applying statistical tests
3. **Visualization**: Use appropriate charts to understand data patterns
4. **Reproducibility**: Document all analysis steps and parameters
5. **Sample Size**: Ensure adequate sample sizes for statistical power
6. **Multiple Testing**: Adjust for multiple comparisons when necessary
7. **Interpretation**: Consider practical significance, not just statistical significance

## Conclusion

Ruby provides powerful capabilities for comprehensive data analysis, from data cleaning and preprocessing to advanced statistical methods. While specialized statistical software exists, Ruby's flexibility and extensive libraries make it an excellent choice for custom analysis workflows and integration with web applications.

## Further Reading

- [Ruby Data Science](https://github.com/SciRuby)
- [Daru - Data Analysis in Ruby](https://github.com/SciRuby/daru)
- [Statsample - Statistical Analysis](https://github.com/SciRuby/statsample)
- [Data Science Best Practices](https://www.datascienceassociation.org/career-center/ethics/)
