# Report Generator - Creates formatted reports

class ReportGenerator
  def initialize(analyzer)
    @analyzer = analyzer
  end

  def generate_report(options = {})
    report_sections = []
    
    # Header
    report_sections << generate_header
    
    # Summary statistics
    report_sections << generate_summary
    
    # Column analysis
    if options[:column]
      report_sections << generate_column_analysis(options[:column])
    else
      report_sections << generate_all_columns_analysis
    end
    
    # Correlations
    if options[:stats]
      report_sections << generate_correlations
    end
    
    # Outliers
    if options[:stats]
      report_sections << generate_outliers
    end
    
    # Footer
    report_sections << generate_footer
    
    report_sections.join("\n\n")
  end

  private

  def generate_header
    <<~HEADER
      ╔══════════════════════════════════════════════════════════════╗
      ║                        DATA ANALYSIS REPORT                      ║
      ╚══════════════════════════════════════════════════════════════╝
      
      Generated on: #{Time.now.strftime('%Y-%m-%d %H:%M:%S')}
      Records analyzed: #{@analyzer.data.length}
    HEADER
  end

  def generate_summary
    summary = @analyzer.summary_statistics
    
    <<~SUMMARY
      ════════════════════════════════════════════════════════════════
      SUMMARY STATISTICS
      ════════════════════════════════════════════════════════════════
      
      Total Records: #{summary[:total_records]}
      Total Columns: #{summary[:total_columns]}
      Numeric Columns: #{summary[:numeric_columns]}
      Text Columns: #{summary[:text_columns]}
      
      Null Counts by Column:
      #{format_null_counts(summary[:null_counts])}
    SUMMARY
  end

  def generate_column_analysis(column_name)
    analysis = @analyzer.analyze_column(column_name)
    return "No data available for column: #{column_name}" unless analysis

    stats = analysis[:statistics]
    dist = analysis[:distribution]
    
    <<~COLUMN
      ════════════════════════════════════════════════════════════════
      COLUMN ANALYSIS: #{column_name.upcase}
      ════════════════════════════════════════════════════════════════
      
      Basic Statistics:
      • Count: #{stats[:count]}
      • Mean: #{format_number(stats[:mean])}
      • Median: #{format_number(stats[:median])}
      • Mode: #{stats[:mode] ? format_number(stats[:mode]) : 'N/A'}
      • Standard Deviation: #{format_number(stats[:std_dev])}
      • Variance: #{format_number(stats[:variance])}
      
      Range:
      • Minimum: #{format_number(stats[:min])}
      • Maximum: #{format_number(stats[:max])}
      • Range: #{format_number(stats[:range])}
      
      Quartiles:
      • Q1 (25%): #{format_number(stats[:quartiles][:q1])}
      • Q2 (50%): #{format_number(stats[:quartiles][:q2])}
      • Q3 (75%): #{format_number(stats[:quartiles][:q3])}
      
      Top Values:
      #{format_top_values(dist[:distribution])}
    COLUMN
  end

  def generate_all_columns_analysis
    sections = []
    
    @analyzer.headers.each do |header|
      if @analyzer.numeric_column?(header)
        sections << generate_column_analysis(header)
      else
        text_analysis = @analyzer.analyze_text_column(header)
        sections << generate_text_column_analysis(text_analysis)
      end
    end
    
    sections.join("\n\n")
  end

  def generate_text_column_analysis(analysis)
    return "No data available for column: #{analysis[:column]}" unless analysis

    <<~TEXT_COLUMN
      ════════════════════════════════════════════════════════════════
      TEXT COLUMN ANALYSIS: #{analysis[:column].upcase}
      ════════════════════════════════════════════════════════════════
      
      Basic Statistics:
      • Count: #{analysis[:count]}
      • Unique Values: #{analysis[:unique_values]}
      • Average Length: #{format_number(analysis[:average_length])}
      
      Most Common Values:
      #{format_text_top_values(analysis[:most_common])}
    TEXT_COLUMN
  end

  def generate_correlations
    correlations = @analyzer.correlations
    return "No correlations available (need at least 2 numeric columns)" if correlations.empty?

    <<~CORRELATIONS
      ════════════════════════════════════════════════════════════════
      CORRELATION ANALYSIS
      ════════════════════════════════════════════════════════════════
      
      #{format_correlations(correlations)}
      
      Interpretation:
      • 0.0 to 0.3: Weak correlation
      • 0.3 to 0.7: Moderate correlation
      • 0.7 to 1.0: Strong correlation
      • Negative values indicate inverse correlation
    CORRELATIONS
  end

  def generate_outliers
    numeric_cols = @analyzer.get_numeric_columns
    return "No numeric columns for outlier analysis" if numeric_cols.empty?

    sections = []
    
    numeric_cols.each do |col|
      outliers = @analyzer.find_outliers(col)
      sections << generate_column_outliers(col, outliers)
    end
    
    sections.join("\n\n")
  end

  def generate_column_outliers(column_name, outliers)
    <<~OUTLIERS
      ════════════════════════════════════════════════════════════════
      OUTLIERS: #{column_name.upcase}
      ════════════════════════════════════════════════════════════════
      
      Outliers found: #{outliers.length}
      
      #{format_outliers(outliers)}
    OUTLIERS
  end

  def generate_footer
    <<~FOOTER
      ════════════════════════════════════════════════════════════════
      REPORT SUMMARY
      ════════════════════════════════════════════════════════════════
      
      This report was generated using the Ruby Data Analysis Tool.
      For more detailed analysis, consider exporting to CSV or using
      additional statistical methods.
      
      End of Report
    FOOTER
  end

  def format_null_counts(null_counts)
    null_counts.map { |col, count| "  • #{col}: #{count}" }.join("\n")
  end

  def format_number(num)
    if num.is_a?(Float)
      sprintf('%.2f', num)
    else
      num.to_s
    end
  end

  def format_top_values(distribution)
    return "No values found" if distribution.empty?
    
    distribution.first(5).map { |value, count|
      "  • #{value}: #{count} occurrences"
    }.join("\n")
  end

  def format_text_top_values(most_common)
    return "No values found" if most_common.empty?
    
    most_common.map { |value, count|
      "  • #{value}: #{count} occurrences"
    }.join("\n")
  end

  def format_correlations(correlations)
    correlations.map { |pair, corr|
      strength = correlation_strength(corr)
      "  • #{pair}: #{format_number(corr)} (#{strength})"
    }.join("\n")
  end

  def correlation_strength(value)
    abs_value = value.abs
    case abs_value
    when 0.0..0.3
      "Weak"
    when 0.3..0.7
      "Moderate"
    when 0.7..1.0
      "Strong"
    else
      "Very Strong"
    end
  end

  def format_outliers(outliers)
    return "No outliers found" if outliers.empty?
    
    "Outlier values:\n#{outliers.map { |v| "  • #{format_number(v)}" }.join("\n")}"
  end
end
