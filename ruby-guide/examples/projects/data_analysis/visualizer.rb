# Visualizer - Basic console visualization

class Visualizer
  def initialize(analyzer)
    @analyzer = analyzer
  end

  def show_charts
    puts "\n" + "=" * 60
    puts "DATA VISUALIZATION"
    puts "=" * 60
    
    numeric_cols = @analyzer.get_numeric_columns
    
    numeric_cols.each do |col|
      show_histogram(col)
      show_box_plot(col)
    end
    
    show_correlation_heatmap if numeric_cols.length > 1
  end

  def show_histogram(column_name, bins = 10)
    values = @analyzer.get_numeric_values(column_name)
    return if values.empty?

    min_val = values.min
    max_val = values.max
    range = max_val - min_val
    bin_width = range / bins.to_f

    # Create bins
    histogram = Array.new(bins, 0)
    values.each do |value|
      bin_index = [(value - min_val) / bin_width, bins - 1].min.floor
      histogram[bin_index] += 1
    end

    puts "\nHISTOGRAM: #{column_name.upcase}"
    puts "-" * 40
    
    max_count = histogram.max
    histogram.each_with_index do |count, i|
      bin_start = min_val + (i * bin_width)
      bin_end = bin_start + bin_width
      
      # Create bar
      bar_length = (count.to_f / max_count * 30).round
      bar = "█" * bar_length
      
      printf "%8.1f - %8.1f | %s %d\n", bin_start, bin_end, bar, count
    end
  end

  def show_box_plot(column_name)
    values = @analyzer.get_numeric_values(column_name)
    return if values.empty?

    stats = @analyzer.analyze_column(column_name)[:statistics]
    q1 = stats[:quartiles][:q1]
    median = stats[:quartiles][:q2]
    q3 = stats[:quartiles][:q3]
    min_val = stats[:min]
    max_val = stats[:max]

    puts "\nBOX PLOT: #{column_name.upcase}"
    puts "-" * 40
    
    # Scale the plot
    scale = 30.0 / (max_val - min_val)
    
    # Calculate positions
    q1_pos = (q1 - min_val) * scale
    median_pos = (median - min_val) * scale
    q3_pos = (q3 - min_val) * scale
    
    # Draw the box plot
    puts "    " + "─" * 32
    puts "    " + "█" * q1_pos.round + "░" * (median_pos - q1_pos).round + "█" * (q3_pos - median_pos).round + "░" * (30 - q3_pos).round + "█"
    puts "    " + "─" * q1_pos.round + "┴" + "─" * (median_pos - q1_pos - 1).round + "┼" + "─" * (q3_pos - median_pos - 1).round + "┴" + "─" * (30 - q3_pos).round
    puts "Min: #{format_number(min_val).rjust(8)} Q1: #{format_number(q1).rjust(8)} Med: #{format_number(median).rjust(8)} Q3: #{format_number(q3).rjust(8)} Max: #{format_number(max_val).rjust(8)}"
  end

  def show_correlation_heatmap
    correlations = @analyzer.correlations
    return if correlations.empty?

    puts "\nCORRELATION HEATMAP"
    puts "-" * 40
    
    numeric_cols = @analyzer.get_numeric_columns
    n = numeric_cols.length
    
    # Print header
    printf "%-12s", ""
    numeric_cols.each { |col| printf "%-8s", col[0..6] }
    puts
    
    # Print rows
    numeric_cols.each_with_index do |row_col, i|
      printf "%-12s", row_col[0..10]
      
      numeric_cols.each_with_index do |col_col, j|
        if i == j
          printf "%-8s", "1.00"
        else
          key = i < j ? "#{row_col} vs #{col_col}" : "#{col_col} vs #{row_col}"
          corr = correlations[key] || 0
          printf "%-8s", format_number(corr)
        end
      end
      puts
    end
    
    puts "\nLegend:"
    puts "█ 1.00: Perfect correlation (diagonal)"
    puts "Values range from -1.00 to 1.00"
  end

  def show_bar_chart(column_name, limit = 10)
    values = @analyzer.get_text_values(column_name)
    return if values.empty?

    freq = values.group_by(&:itself).transform_values(&:count)
    sorted_freq = freq.sort_by { |_, count| -count }.first(limit)

    puts "\nBAR CHART: #{column_name.upcase} (Top #{limit})"
    puts "-" * 40
    
    max_count = sorted_freq.map(&:last).max
    sorted_freq.each do |value, count|
      bar_length = (count.to_f / max_count * 30).round
      bar = "█" * bar_length
      
      printf "%-15s | %s %d\n", value[0..14], bar, count
    end
  end

  def show_pie_chart(column_name, limit = 6)
    values = @analyzer.get_text_values(column_name)
    return if values.empty?

    freq = values.group_by(&:itself).transform_values(&:count)
    total = freq.values.sum
    sorted_freq = freq.sort_by { |_, count| -count }.first(limit)

    puts "\nPIE CHART: #{column_name.upcase} (Top #{limit})"
    puts "-" * 40
    
    sorted_freq.each_with_index do |(value, count), i|
      percentage = (count.to_f / total * 100).round(1)
      
      # Use different characters for different segments
      chars = ['█', '▓', '▒', '░', '▄', '▌']
      char = chars[i % chars.length]
      
      puts "#{char} #{value[0..15]}: #{percentage}% (#{count})"
    end
    
    puts "\nTotal: #{total} records"
  end

  def show_scatter_plot(x_column, y_column)
    x_values = @analyzer.get_numeric_values(x_column)
    y_values = @analyzer.get_numeric_values(y_column)
    return if x_values.empty? || y_values.empty?

    puts "\nSCATTER PLOT: #{x_column.upcase} vs #{y_column.upcase}"
    puts "-" * 50
    
    # Create a simple ASCII scatter plot
    x_min, x_max = x_values.minmax
    y_min, y_max = y_values.minmax
    
    # Create grid
    grid_height = 20
    grid_width = 40
    
    # Initialize grid
    grid = Array.new(grid_height) { Array.new(grid_width, ' ') }
    
    # Plot points
    x_values.each_with_index do |x, i|
      y = y_values[i]
      
      x_pos = ((x - x_min) / (x_max - x_min) * (grid_width - 1)).round
      y_pos = grid_height - 1 - ((y - y_min) / (y_max - y_min) * (grid_height - 1)).round
      
      x_pos = [0, [x_pos, grid_width - 1].min].max
      y_pos = [0, [y_pos, grid_height - 1].min].max
      
      grid[y_pos][x_pos] = '●'
    end
    
    # Draw grid
    grid.each_with_index do |row, i|
      print "#{format_number(y_min + (y_max - y_min) * (grid_height - 1 - i) / (grid_height - 1)).rjust(8)} │"
      row.each { |cell| print cell }
      puts
    end
    
    # X-axis
    puts "         └" + "─" * grid_width
    print "         "
    (0..grid_width - 1).step(8) do |i|
      x_val = x_min + (x_max - x_min) * i / (grid_width - 1)
      print format_number(x_val).rjust(8)
    end
    puts
    
    puts "\nCorrelation: #{format_number(@analyzer.correlations["#{x_column} vs #{y_column}"] || 0)}"
  end

  private

  def format_number(num)
    if num.is_a?(Float)
      sprintf('%.2f', num)
    else
      num.to_s
    end
  end
end
