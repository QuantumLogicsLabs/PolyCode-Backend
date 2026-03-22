# Statistics Module - Statistical calculations

module Statistics
  def self.mean(values)
    return 0 if values.empty?
    values.sum.to_f / values.length
  end

  def self.median(values)
    return 0 if values.empty?
    
    sorted = values.sort
    mid = sorted.length / 2
    
    if sorted.length.odd?
      sorted[mid]
    else
      (sorted[mid - 1] + sorted[mid]) / 2.0
    end
  end

  def self.mode(values)
    return nil if values.empty?
    
    frequency = values.group_by(&:itself).transform_values(&:count)
    max_freq = frequency.values.max
    modes = frequency.select { |_, v| v == max_freq }.keys
    
    modes.length == 1 ? modes.first : modes
  end

  def self.variance(values)
    return 0 if values.empty?
    
    m = mean(values)
    sum_of_squares = values.map { |v| (v - m) ** 2 }.sum
    sum_of_squares / values.length
  end

  def self.standard_deviation(values)
    Math.sqrt(variance(values))
  end

  def self.percentile(values, percentile)
    return 0 if values.empty?
    
    sorted = values.sort
    index = (percentile / 100.0) * (sorted.length - 1)
    
    if index == index.to_i
      sorted[index.to_i]
    else
      lower = sorted[index.floor]
      upper = sorted[index.ceil]
      lower + (upper - lower) * (index - index.floor)
    end
  end

  def self.quartiles(values)
    {
      q1: percentile(values, 25),
      q2: percentile(values, 50), # median
      q3: percentile(values, 75)
    }
  end

  def self.range(values)
    return 0 if values.empty?
    values.max - values.min
  end

  def self.correlation(x_values, y_values)
    return 0 if x_values.empty? || y_values.empty? || x_values.length != y_values.length
    
    n = x_values.length
    x_mean = mean(x_values)
    y_mean = mean(y_values)
    
    numerator = (0...n).sum { |i| (x_values[i] - x_mean) * (y_values[i] - y_mean) }
    
    x_sum_sq = (0...n).sum { |i| (x_values[i] - x_mean) ** 2 }
    y_sum_sq = (0...n).sum { |i| (y_values[i] - y_mean) ** 2 }
    
    denominator = Math.sqrt(x_sum_sq * y_sum_sq)
    
    denominator == 0 ? 0 : numerator / denominator
  end

  def self.summary(values)
    return {} if values.empty?
    
    {
      count: values.length,
      mean: mean(values),
      median: median(values),
      mode: mode(values),
      std_dev: standard_deviation(values),
      variance: variance(values),
      min: values.min,
      max: values.max,
      range: range(values),
      quartiles: quartiles(values)
    }
  end

  def self.frequency_distribution(values)
    freq = values.group_by(&:itself).transform_values(&:count)
    
    # Sort by frequency (descending)
    sorted_freq = freq.sort_by { |_, count| -count }
    
    {
      total: values.length,
      unique: freq.length,
      distribution: sorted_freq
    }
  end
end
