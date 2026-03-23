# Scientific Computing in Ruby

## Overview

Scientific computing involves using computers to solve scientific and engineering problems through numerical methods, simulations, and data analysis. Ruby provides excellent tools for scientific computing, from numerical computations to visualization and modeling.

## Numerical Methods

### Numerical Analysis Framework
```ruby
class NumericalMethods
  def self.newton_raphson(f, df, x0, tolerance = 1e-10, max_iterations = 100)
    x = x0
    iterations = 0
    
    while iterations < max_iterations
      fx = f.call(x)
      
      if fx.abs < tolerance
        return { root: x, iterations: iterations, converged: true }
      end
      
      dfx = df.call(x)
      
      if dfx.abs < tolerance
        return { root: x, iterations: iterations, converged: false, error: "Derivative too small" }
      end
      
      x_new = x - fx / dfx
      
      if (x_new - x).abs < tolerance
        return { root: x_new, iterations: iterations + 1, converged: true }
      end
      
      x = x_new
      iterations += 1
    end
    
    { root: x, iterations: iterations, converged: false, error: "Maximum iterations reached" }
  end

  def self.bisection_method(f, a, b, tolerance = 1e-10, max_iterations = 100)
    fa = f.call(a)
    fb = f.call(b)
    
    if fa * fb > 0
      return { error: "Function has same sign at both endpoints" }
    end
    
    iterations = 0
    
    while iterations < max_iterations && (b - a).abs > tolerance
      c = (a + b) / 2.0
      fc = f.call(c)
      
      if fc.abs < tolerance
        return { root: c, iterations: iterations, converged: true }
      end
      
      if fa * fc < 0
        b = c
        fb = fc
      else
        a = c
        fa = fc
      end
      
      iterations += 1
    end
    
    { root: (a + b) / 2.0, iterations: iterations, converged: (b - a).abs <= tolerance }
  end

  def self.secant_method(f, x0, x1, tolerance = 1e-10, max_iterations = 100)
    iterations = 0
    
    while iterations < max_iterations
      fx0 = f.call(x0)
      fx1 = f.call(x1)
      
      if fx1.abs < tolerance
        return { root: x1, iterations: iterations, converged: true }
      end
      
      if (fx1 - fx0).abs < tolerance
        return { root: x1, iterations: iterations, converged: false, error: "Function values too close" }
      end
      
      x2 = x1 - fx1 * (x1 - x0) / (fx1 - fx0)
      
      if (x2 - x1).abs < tolerance
        return { root: x2, iterations: iterations + 1, converged: true }
      end
      
      x0, x1 = x1, x2
      iterations += 1
    end
    
    { root: x1, iterations: iterations, converged: false, error: "Maximum iterations reached" }
  end

  def self.runge_kutta_4(f, y0, t0, tf, h = 0.1)
    n = ((tf - t0) / h).ceil
    t = t0
    y = y0
    
    solution = [[t, y]]
    
    n.times do |i|
      k1 = h * f.call(t, y)
      k2 = h * f.call(t + h/2, y + k1/2)
      k3 = h * f.call(t + h/2, y + k2/2)
      k4 = h * f.call(t + h, y + k3)
      
      y = y + (k1 + 2*k2 + 2*k3 + k4) / 6
      t = t + h
      
      solution << [t, y]
    end
    
    solution
  end

  def self.euler_method(f, y0, t0, tf, h = 0.1)
    n = ((tf - t0) / h).ceil
    t = t0
    y = y0
    
    solution = [[t, y]]
    
    n.times do |i|
      y = y + h * f.call(t, y)
      t = t + h
      solution << [t, y]
    end
    
    solution
  end

  def self.trapezoidal_rule(f, a, b, n = 1000)
    h = (b - a) / n
    sum = f.call(a) + f.call(b)
    
    (1...n).each do |i|
      sum += 2 * f.call(a + i * h)
    end
    
    sum * h / 2
  end

  def self.simpsons_rule(f, a, b, n = 1000)
    return trapezoidal_rule(f, a, b, n) if n.odd?
    
    h = (b - a) / n
    sum = f.call(a) + f.call(b)
    
    (1...n).step(2) do |i|
      sum += 4 * f.call(a + i * h)
    end
    
    (2...n).step(2) do |i|
      sum += 2 * f.call(a + i * h)
    end
    
    sum * h / 3
  end

  def self.gaussian_quadrature(f, a, b, n = 5)
    # Gauss-Legendre quadrature points and weights
    points_weights = {
      2 => [[-0.5773502692, 0.5773502692], [1.0, 1.0]],
      3 => [[-0.7745966692, 0.0, 0.7745966692], [0.5555555556, 0.8888888889, 0.5555555556]],
      4 => [[-0.8611363116, -0.3399810436, 0.3399810436, 0.8611363116], 
            [0.3478548451, 0.6521451549, 0.6521451549, 0.3478548451]],
      5 => [[-0.9061798459, -0.5384693101, 0.0, 0.5384693101, 0.9061798459],
            [0.2369268850, 0.4786286705, 0.5688888889, 0.4786286705, 0.2369268850]]
    }
    
    points, weights = points_weights[n]
    
    # Transform from [-1, 1] to [a, b]
    transformed_points = points.map { |x| 0.5 * (b - a) * x + 0.5 * (b + a) }
    transformed_weights = weights.map { |w| 0.5 * (b - a) * w }
    
    transformed_points.zip(transformed_weights).sum { |x, w| w * f.call(x) }
  end

  def self.matrix_multiply(a, b)
    a.map do |row|
      b.transpose.map do |col|
        row.zip(col).sum { |x, y| x * y }
      end
    end
  end

  def self.matrix_transpose(matrix)
    matrix.transpose
  end

  def self.lu_decomposition(matrix)
    n = matrix.length
    l = Array.new(n) { Array.new(n, 0) }
    u = Array.new(n) { Array.new(n, 0) }
    
    (0...n).each do |i|
      # Upper triangular matrix
      (i...n).each do |j|
        sum = (0...i).sum { |k| l[i][k] * u[k][j] }
        u[i][j] = matrix[i][j] - sum
      end
      
      # Lower triangular matrix
      (i...n).each do |j|
        if i == j
          l[i][i] = 1
        else
          sum = (0...i).sum { |k| l[j][k] * u[k][i] }
          l[j][i] = (matrix[j][i] - sum) / u[i][i]
        end
      end
    end
    
    { l: l, u: u }
  end

  def self.solve_linear_system_lu(l, u, b)
    n = l.length
    
    # Forward substitution (Ly = b)
    y = Array.new(n, 0)
    (0...n).each do |i|
      sum = (0...i).sum { |j| l[i][j] * y[j] }
      y[i] = b[i] - sum
    end
    
    # Back substitution (Ux = y)
    x = Array.new(n, 0)
    (n - 1).downto(0) do |i|
      sum = (i + 1...n).sum { |j| u[i][j] * x[j] }
      x[i] = (y[i] - sum) / u[i][i]
    end
    
    x
  end
end
```

### Optimization Algorithms
```ruby
class OptimizationMethods
  def self.gradient_descent(f, df, x0, learning_rate = 0.01, tolerance = 1e-6, max_iterations = 1000)
    x = x0.map(&:to_f)
    iterations = 0
    
    while iterations < max_iterations
      gradient = df.call(x)
      gradient_norm = Math.sqrt(gradient.sum { |g| g ** 2 })
      
      if gradient_norm < tolerance
        return { solution: x, iterations: iterations, converged: true }
      end
      
      x = x.zip(gradient).map { |xi, gi| xi - learning_rate * gi }
      iterations += 1
    end
    
    { solution: x, iterations: iterations, converged: false, error: "Maximum iterations reached" }
  end

  def self.stochastic_gradient_descent(f, df, x0, data, learning_rate = 0.01, epochs = 100, batch_size = 1)
    x = x0.map(&:to_f)
    n = data.length
    
    epochs.times do |epoch|
      # Shuffle data
      shuffled_data = data.shuffle
      
      # Process in batches
      (0...n).step(batch_size) do |i|
        batch = shuffled_data[i...[i + batch_size, n].min]
        
        # Calculate gradient for batch
        batch_gradient = batch.map { |point| df.call(x, point) }.reduce do |sum, grad|
          sum.zip(grad).map { |s, g| s + g }
        end
        
        batch_gradient = batch_gradient.map { |g| g / batch.length }
        
        # Update parameters
        x = x.zip(batch_gradient).map { |xi, gi| xi - learning_rate * gi }
      end
      
      # Optional: decay learning rate
      learning_rate *= 0.99 if epoch % 10 == 0
    end
    
    { solution: x, epochs: epochs }
  end

  def self.newton_optimization(f, df, d2f, x0, tolerance = 1e-6, max_iterations = 100)
    x = x0.map(&:to_f)
    iterations = 0
    
    while iterations < max_iterations
      gradient = df.call(x)
      hessian = d2f.call(x)
      
      gradient_norm = Math.sqrt(gradient.sum { |g| g ** 2 })
      
      if gradient_norm < tolerance
        return { solution: x, iterations: iterations, converged: true }
      end
      
      # Solve Hessian * delta = -gradient
      delta = solve_linear_system(hessian, gradient.map { |g| -g })
      
      x = x.zip(delta).map { |xi, di| xi + di }
      iterations += 1
    end
    
    { solution: x, iterations: iterations, converged: false, error: "Maximum iterations reached" }
  end

  def self.simulated_annealing(f, x0, bounds, max_iterations = 1000, initial_temp = 100.0, cooling_rate = 0.95)
    current_x = x0.dup
    current_y = f.call(current_x)
    best_x = current_x.dup
    best_y = current_y
    temp = initial_temp
    
    max_iterations.times do |iteration|
      # Generate neighbor solution
      neighbor_x = generate_neighbor(current_x, bounds)
      neighbor_y = f.call(neighbor_x)
      
      # Accept or reject
      delta = neighbor_y - current_y
      
      if delta < 0 || rand < Math.exp(-delta / temp)
        current_x = neighbor_x
        current_y = neighbor_y
        
        if current_y < best_y
          best_x = current_x.dup
          best_y = current_y
        end
      end
      
      # Cool down
      temp *= cooling_rate
    end
    
    { solution: best_x, value: best_y }
  end

  def self.genetic_algorithm(f, population_size, bounds, generations = 100, mutation_rate = 0.1, crossover_rate = 0.8)
    dimensions = bounds.length
    
    # Initialize population
    population = Array.new(population_size) do
      bounds.map { |min, max| rand(min..max) }
    end
    
    generations.times do |generation|
      # Evaluate fitness
      fitness = population.map { |individual| 1.0 / (1.0 + f.call(individual)) }
      
      # Selection (tournament)
      selected = tournament_selection(population, fitness, population_size)
      
      # Crossover
      offspring = []
      selected.each_slice(2) do |parent1, parent2|
        if rand < crossover_rate && parent2
          child1, child2 = crossover(parent1, parent2)
          offspring << child1 << child2
        else
          offspring << parent1
          offspring << parent2 if parent2
        end
      end
      
      # Mutation
      offspring.each { |individual| mutate(individual, bounds, mutation_rate) }
      
      # Replace population
      population = offspring[0...population_size]
    end
    
    # Return best solution
    best_individual = population.min_by { |individual| f.call(individual) }
    
    { solution: best_individual, value: f.call(best_individual) }
  end

  def self.particle_swarm_optimization(f, bounds, swarm_size = 30, max_iterations = 100, w = 0.7, c1 = 1.5, c2 = 1.5)
    dimensions = bounds.length
      
    # Initialize swarm
    particles = Array.new(swarm_size) do
      position = bounds.map { |min, max| rand(min..max) }
      velocity = bounds.map { |min, max| rand(-1..1) * (max - min) * 0.1 }
      personal_best = position.dup
      personal_best_value = f.call(position)
      
      {
        position: position,
        velocity: velocity,
        personal_best: personal_best,
        personal_best_value: personal_best_value
      }
    end
    
    # Global best
    global_best = particles.min_by { |p| p[:personal_best_value] }
    
    max_iterations.times do |iteration|
      particles.each do |particle|
        dimensions.times do |d|
          # Update velocity
          r1, r2 = rand, rand
          
          particle[:velocity][d] = w * particle[:velocity][d] +
                                  c1 * r1 * (particle[:personal_best][d] - particle[:position][d]) +
                                  c2 * r2 * (global_best[:personal_best][d] - particle[:position][d])
          
          # Update position
          particle[:position][d] += particle[:velocity][d]
          
          # Apply bounds
          particle[:position][d] = [[bounds[d][0], particle[:position][d]].max, bounds[d][1]].min
        end
        
        # Update personal best
        current_value = f.call(particle[:position])
        if current_value < particle[:personal_best_value]
          particle[:personal_best] = particle[:position].dup
          particle[:personal_best_value] = current_value
          
          # Update global best
          if current_value < global_best[:personal_best_value]
            global_best = {
              personal_best: particle[:position].dup,
              personal_best_value: current_value
            }
          end
        end
      end
    end
    
    { solution: global_best[:personal_best], value: global_best[:personal_best_value] }
  end

  private

  def self.solve_linear_system(a, b)
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

  def self.generate_neighbor(x, bounds)
    neighbor = x.dup
    
    x.each_with_index do |_, i|
      if rand < 0.1  # 10% chance to modify each dimension
        min, max = bounds[i]
        range = max - min
        neighbor[i] += rand(-range * 0.1..range * 0.1)
        neighbor[i] = [[min, neighbor[i]].max, max].min
      end
    end
    
    neighbor
  end

  def self.tournament_selection(population, fitness, tournament_size = 3)
    selected = []
    
    population_size.times do
      tournament = population_size.times.to_a.sample(tournament_size)
      winner = tournament.max_by { |i| fitness[i] }
      selected << population[winner]
    end
    
    selected
  end

  def self.crossover(parent1, parent2)
    crossover_point = rand(parent1.length)
    
    child1 = parent1[0...crossover_point] + parent2[crossover_point..-1]
    child2 = parent2[0...crossover_point] + parent1[crossover_point..-1]
    
    [child1, child2]
  end

  def self.mutate(individual, bounds, mutation_rate)
    individual.each_with_index do |_, i|
      if rand < mutation_rate
        min, max = bounds[i]
        individual[i] = rand(min..max)
      end
    end
  end
end
```

## Signal Processing

### Digital Signal Processing
```ruby
class SignalProcessing
  def self.fft(signal)
    n = signal.length
    return signal if n == 1
    
    # Check if n is power of 2
    if (n & (n - 1)) != 0
      # Pad with zeros to next power of 2
      padded_n = 2 ** Math.log2(n).ceil
      signal = signal + Array.new(padded_n - n, 0)
      n = padded_n
    end
    
    # Cooley-Tukey FFT algorithm
    fft_recursive(signal)
  end

  def self.ifft(spectrum)
    n = spectrum.length
    conjugated = spectrum.map { |x| x.conjugate }
    fft_result = fft(conjugated)
    fft_result.map { |x| x.conjugate / n }
  end

  def self.dft(signal)
    n = signal.length
    spectrum = Array.new(n) do |k|
      sum = Complex(0, 0)
      
      n.times do |j|
        angle = -2 * Math::PI * k * j / n
        sum += signal[j] * Complex(Math.cos(angle), Math.sin(angle))
      end
      
      sum
    end
    
    spectrum
  end

  def self.convolution(signal1, signal2)
    n1 = signal1.length
    n2 = signal2.length
    n = n1 + n2 - 1
    
    # Pad signals to length n
    padded_signal1 = signal1 + Array.new(n - n1, 0)
    padded_signal2 = signal2 + Array.new(n - n2, 0)
    
    # Convolution using FFT
    fft1 = fft(padded_signal1)
    fft2 = fft(padded_signal2)
    
    product = fft1.zip(fft2).map { |x, y| x * y }
    ifft(product)[0...n].map(&:real)
  end

  def self.correlation(signal1, signal2)
    n1 = signal1.length
    n2 = signal2.length
    n = n1 + n2 - 1
    
    # Pad signals
    padded_signal1 = signal1 + Array.new(n - n1, 0)
    padded_signal2 = signal2 + Array.new(n - n2, 0)
    
    # Correlation using FFT
    fft1 = fft(padded_signal1)
    fft2 = fft(padded_signal2)
    
    product = fft1.zip(fft2).map { |x, y| x * y.conjugate }
    ifft(product)[0...n].map(&:real)
  end

  def self.filter(signal, filter_type, cutoff, sampling_rate)
    case filter_type
    when :low_pass
      low_pass_filter(signal, cutoff, sampling_rate)
    when :high_pass
      high_pass_filter(signal, cutoff, sampling_rate)
    when :band_pass
      band_pass_filter(signal, cutoff[0], cutoff[1], sampling_rate)
    when :band_stop
      band_stop_filter(signal, cutoff[0], cutoff[1], sampling_rate)
    else
      signal
    end
  end

  def self.low_pass_filter(signal, cutoff, sampling_rate)
    n = signal.length
    nyquist = sampling_rate / 2.0
    normalized_cutoff = cutoff / nyquist
    
    # Simple FIR low-pass filter
    filter_order = 51
    filter_coefficients = Array.new(filter_order) do |i|
      i -= filter_order / 2
      
      if i == 0
        2 * normalized_cutoff
      else
        2 * normalized_cutoff * Math.sin(2 * Math::PI * normalized_cutoff * i) / (2 * Math::PI * i)
      end
    end
    
    # Apply filter
    convolution(signal, filter_coefficients)
  end

  def self.high_pass_filter(signal, cutoff, sampling_rate)
    # High-pass filter = signal - low-pass filtered signal
    low_passed = low_pass_filter(signal, cutoff, sampling_rate)
    
    signal.each_with_index.map do |value, i|
      i < low_passed.length ? value - low_passed[i] : value
    end
  end

  def self.spectrogram(signal, window_size, overlap)
    n = signal.length
    step = window_size - overlap
    spectrogram_data = []
    
    (0...n - window_size + 1).step(step) do |i|
      window = signal[i...i + window_size]
      
      # Apply window function (Hamming)
      windowed_signal = window.each_with_index.map do |value, j|
        value * (0.54 - 0.46 * Math.cos(2 * Math::PI * j / (window_size - 1)))
      end
      
      # Compute FFT
      spectrum = fft(windowed_signal)
      magnitudes = spectrum.map(&:abs)
      
      spectrogram_data << magnitudes[0...window_size/2]
    end
    
    spectrogram_data
  end

  def self.power_spectral_density(signal, sampling_rate)
    n = signal.length
    spectrum = fft(signal)
    
    # Compute power spectral density
    psd = spectrum[0...n/2].map.with_index do |value, k|
      (value.abs ** 2) / (n * sampling_rate / 2)
    end
    
    frequencies = (0...n/2).map { |k| k * sampling_rate / n }
    
    { frequencies: frequencies, psd: psd }
  end

  def self.resample(signal, original_rate, new_rate)
    ratio = new_rate.to_f / original_rate
    new_length = (signal.length * ratio).ceil
    
    # Linear interpolation
    resampled_signal = Array.new(new_length) do |i|
      original_index = i / ratio
      
      if original_index >= signal.length - 1
        signal.last
      else
        idx1 = original_index.floor
        idx2 = idx1 + 1
        fraction = original_index - idx1
        
        signal[idx1] * (1 - fraction) + signal[idx2] * fraction
      end
    end
    
    resampled_signal
  end

  def self.denoise_wavelet(signal, wavelet_type = :db4, levels = 4)
    # Simplified wavelet denoising
    # In practice, would use a proper wavelet library
    
    # Apply thresholding to high-frequency components
    spectrum = fft(signal)
    threshold = spectrum.map(&:abs).sort[spectrum.length * 0.9].abs
    
    denoised_spectrum = spectrum.map do |value|
      value.abs > threshold ? value : 0
    end
    
    ifft(denoised_spectrum).map(&:real)
  end

  private

  def self.fft_recursive(signal)
    n = signal.length
    return signal if n == 1
    
    # Divide
    even = signal.each_with_index.select { |_, i| i.even? }.map(&:first)
    odd = signal.each_with_index.select { |_, i| i.odd? }.map(&:first)
    
    # Conquer
    even_fft = fft_recursive(even)
    odd_fft = fft_recursive(odd)
    
    # Combine
    spectrum = Array.new(n)
    
    (n/2).times do |k|
      t = Complex(Math.cos(-2 * Math::PI * k / n), Math.sin(-2 * Math::PI * k / n))
      
      spectrum[k] = even_fft[k] + t * odd_fft[k]
      spectrum[k + n/2] = even_fft[k] - t * odd_fft[k]
    end
    
    spectrum
  end
end
```

## Scientific Visualization

### Plotting and Graphing
```ruby
class ScientificPlotter
  def initialize(width = 800, height = 600)
    @width = width
    @height = height
    @data = []
    @styles = {}
    @labels = {}
  end

  def add_data(x_data, y_data, name = nil, style = {})
    data_point = {
      x: x_data,
      y: y_data,
      name: name || "data_#{@data.length + 1}",
      style: default_style.merge(style)
    }
    
    @data << data_point
  end

  def set_title(title)
    @labels[:title] = title
  end

  def set_xlabel(label)
    @labels[:xlabel] = label
  end

  def set_ylabel(label)
    @labels[:ylabel] = label
  end

  def plot_2d(filename = nil)
    # Generate SVG plot
    svg = generate_2d_svg
    
    if filename
      File.write(filename, svg)
      puts "Plot saved to #{filename}"
    else
      puts svg
    end
    
    svg
  end

  def plot_3d(filename = nil)
    # Generate 3D plot (simplified)
    puts "3D plotting not implemented in this simplified version"
  end

  def histogram(data, bins = 10, filename = nil)
    hist_data = calculate_histogram(data, bins)
    
    svg = generate_histogram_svg(hist_data)
    
    if filename
      File.write(filename, svg)
      puts "Histogram saved to #{filename}"
    else
      puts svg
    end
    
    svg
  end

  def scatter_plot(x_data, y_data, filename = nil)
    add_data(x_data, y_data, "scatter", { type: :scatter })
    plot_2d(filename)
  end

  def line_plot(x_data, y_data, filename = nil)
    add_data(x_data, y_data, "line", { type: :line })
    plot_2d(filename)
  end

  def contour_plot(x_data, y_data, z_data, filename = nil)
    # Simplified contour plot
    puts "Contour plotting not implemented in this simplified version"
  end

  private

  def default_style
    {
      type: :line,
      color: :blue,
      width: 2,
      marker: :none
    }
  end

  def calculate_2d_bounds
    all_x = @data.flat_map { |d| d[:x] }
    all_y = @data.flat_map { |d| d[:y] }
    
    {
      x_min: all_x.min,
      x_max: all_x.max,
      y_min: all_y.min,
      y_max: all_y.max
    }
  end

  def generate_2d_svg
    bounds = calculate_2d_bounds
    margin = 50
    
    svg_width = @width
    svg_height = @height
    plot_width = svg_width - 2 * margin
    plot_height = svg_height - 2 * margin
    
    svg = []
    svg << "<svg width='#{svg_width}' height='#{svg_height}' xmlns='http://www.w3.org/2000/svg'>"
    
    # Background
    svg << "<rect width='#{svg_width}' height='#{svg_height}' fill='white'/>"
    
    # Plot area
    svg << "<rect x='#{margin}' y='#{margin}' width='#{plot_width}' height='#{plot_height}' fill='none' stroke='black'/>"
    
    # Grid lines
    draw_grid(svg, bounds, margin, plot_width, plot_height)
    
    # Axes
    draw_axes(svg, bounds, margin, plot_width, plot_height)
    
    # Data
    @data.each { |data| draw_data(svg, data, bounds, margin, plot_width, plot_height) }
    
    # Labels
    draw_labels(svg, margin, plot_width, plot_height)
    
    svg << "</svg>"
    svg.join("\n")
  end

  def draw_grid(svg, bounds, margin, plot_width, plot_height)
    # Vertical grid lines
    x_ticks = 10
    (0..x_ticks).each do |i|
      x = margin + (i * plot_width / x_ticks)
      svg << "<line x1='#{x}' y1='#{margin}' x2='#{x}' y2='#{margin + plot_height}' stroke='lightgray' stroke-dasharray='2,2'/>"
    end
    
    # Horizontal grid lines
    y_ticks = 10
    (0..y_ticks).each do |i|
      y = margin + (i * plot_height / y_ticks)
      svg << "<line x1='#{margin}' y1='#{y}' x2='#{margin + plot_width}' y2='#{y}' stroke='lightgray' stroke-dasharray='2,2'/>"
    end
  end

  def draw_axes(svg, bounds, margin, plot_width, plot_height)
    # X-axis
    svg << "<line x1='#{margin}' y1='#{margin + plot_height}' x2='#{margin + plot_width}' y2='#{margin + plot_height}' stroke='black'/>"
    
    # Y-axis
    svg << "<line x1='#{margin}' y1='#{margin}' x2='#{margin}' y2='#{margin + plot_height}' stroke='black'/>"
    
    # X-axis ticks and labels
    x_ticks = 5
    (0..x_ticks).each do |i|
      x = margin + (i * plot_width / x_ticks)
      value = bounds[:x_min] + (i * (bounds[:x_max] - bounds[:x_min]) / x_ticks)
      
      svg << "<line x1='#{x}' y1='#{margin + plot_height}' x2='#{x}' y2='#{margin + plot_height + 5}' stroke='black'/>"
      svg << "<text x='#{x}' y='#{margin + plot_height + 20}' text-anchor='middle' font-size='12'>#{value.round(2)}</text>"
    end
    
    # Y-axis ticks and labels
    y_ticks = 5
    (0..y_ticks).each do |i|
      y = margin + plot_height - (i * plot_height / y_ticks)
      value = bounds[:y_min] + (i * (bounds[:y_max] - bounds[:y_min]) / y_ticks)
      
      svg << "<line x1='#{margin - 5}' y1='#{y}' x2='#{margin}' y2='#{y}' stroke='black'/>"
      svg << "<text x='#{margin - 10}' y='#{y + 5}' text-anchor='end' font-size='12'>#{value.round(2)}</text>"
    end
  end

  def draw_data(svg, data, bounds, margin, plot_width, plot_height)
    case data[:style][:type]
    when :line
      draw_line(svg, data, bounds, margin, plot_width, plot_height)
    when :scatter
      draw_scatter(svg, data, bounds, margin, plot_width, plot_height)
    end
  end

  def draw_line(svg, data, bounds, margin, plot_width, plot_height)
    x_data = data[:x]
    y_data = data[:y]
    color = data[:style][:color]
    width = data[:style][:width]
    
    points = x_data.zip(y_data).map do |x, y|
      px = margin + ((x - bounds[:x_min]) / (bounds[:x_max] - bounds[:x_min])) * plot_width
      py = margin + plot_height - ((y - bounds[:y_min]) / (bounds[:y_max] - bounds[:y_min])) * plot_height
      "#{px},#{py}"
    end
    
    svg << "<polyline points='#{points.join(' ')} fill='none' stroke='#{color}' stroke-width='#{width}'/>"
  end

  def draw_scatter(svg, data, bounds, margin, plot_width, plot_height)
    x_data = data[:x]
    y_data = data[:y]
    color = data[:style][:color]
    marker_size = 3
    
    x_data.zip(y_data).each do |x, y|
      px = margin + ((x - bounds[:x_min]) / (bounds[:x_max] - bounds[:x_min])) * plot_width
      py = margin + plot_height - ((y - bounds[:y_min]) / (bounds[:y_max] - bounds[:y_min])) * plot_height
      
      svg << "<circle cx='#{px}' cy='#{py}' r='#{marker_size}' fill='#{color}'/>"
    end
  end

  def draw_labels(svg, margin, plot_width, plot_height)
    if @labels[:title]
      svg << "<text x='#{@width/2}' y='30' text-anchor='middle' font-size='16' font-weight='bold'>#{@labels[:title]}</text>"
    end
    
    if @labels[:xlabel]
      svg << "<text x='#{margin + plot_width/2}' y='#{@height - 10}' text-anchor='middle' font-size='14'>#{@labels[:xlabel]}</text>"
    end
    
    if @labels[:ylabel]
      svg << "<text x='20' y='#{margin + plot_height/2}' text-anchor='middle' font-size='14' transform='rotate(-90, 20, #{margin + plot_height/2})'>#{@labels[:ylabel]}</text>"
    end
  end

  def calculate_histogram(data, bins)
    min_val = data.min
    max_val = data.max
    bin_width = (max_val - min_val) / bins.to_f
    
    histogram = Array.new(bins) { |i| min_val + i * bin_width }
    counts = Array.new(bins, 0)
    
    data.each do |value|
      bin_index = [(value - min_val) / bin_width, bins - 1].min.floor
      counts[bin_index] += 1
    end
    
    { bins: histogram, counts: counts }
  end

  def generate_histogram_svg(hist_data)
    margin = 50
    plot_width = @width - 2 * margin
    plot_height = @height - 2 * margin
    
    max_count = hist_data[:counts].max
    bin_width = plot_width / hist_data[:bins].length
    
    svg = []
    svg << "<svg width='#{@width}' height='#{@height}' xmlns='http://www.w3.org/2000/svg'>"
    svg << "<rect width='#{@width}' height='#{@height}' fill='white'/>"
    
    # Draw bars
    hist_data[:counts].each_with_index do |count, i|
      bar_height = (count.to_f / max_count) * plot_height
      x = margin + i * bin_width
      y = margin + plot_height - bar_height
      
      svg << "<rect x='#{x}' y='#{y}' width='#{bin_width * 0.8}' height='#{bar_height}' fill='steelblue' stroke='black'/>"
    end
    
    # Draw axes
    svg << "<line x1='#{margin}' y1='#{margin + plot_height}' x2='#{margin + plot_width}' y2='#{margin + plot_height}' stroke='black'/>"
    svg << "<line x1='#{margin}' y1='#{margin}' x2='#{margin}' y2='#{margin + plot_height}' stroke='black'/>"
    
    svg << "</svg>"
    svg.join("\n")
  end
end
```

## Computational Physics

### Physics Simulations
```ruby
class PhysicsSimulator
  def initialize
    @particles = []
    @forces = []
    @time = 0
    @dt = 0.01
  end

  def add_particle(mass, position, velocity, charge = 0)
    particle = {
      mass: mass,
      position: position,
      velocity: velocity,
      acceleration: [0, 0, 0],
      charge: charge,
      force: [0, 0, 0]
    }
    
    @particles << particle
  end

  def add_force(force_type, params = {})
    force = { type: force_type, params: params }
    @forces << force
  end

  def step()
    # Calculate forces
    calculate_forces()
    
    # Update particles using Verlet integration
    @particles.each do |particle|
      # Update position
      particle[:position] = particle[:position].zip(particle[:velocity]).map { |pos, vel| pos + vel * @dt + 0.5 * particle[:acceleration].map { |a| a * @dt ** 2 } }
      
      # Calculate new acceleration
      old_acceleration = particle[:acceleration]
      particle[:acceleration] = particle[:force].map { |f| f / particle[:mass] }
      
      # Update velocity
      particle[:velocity] = particle[:velocity].zip(old_acceleration, particle[:acceleration]).map { |vel, old_acc, new_acc| vel + 0.5 * (old_acc + new_acc) * @dt }
    end
    
    @time += @dt
  end

  def simulate(duration)
    steps = (duration / @dt).to_i
    
    steps.times do |i|
      step()
      
      if i % 100 == 0
        puts "Time: #{@time.round(3)}s, Step: #{i + 1}/#{steps}"
      end
    end
  end

  def gravitational_force(particle1, particle2, g = 6.67430e-11)
    r1 = particle1[:position]
    r2 = particle2[:position]
    
    # Calculate distance vector
    r = r2.zip(r1).map { |x, y| x - y }
    r_mag = Math.sqrt(r.sum { |x| x ** 2 })
    
    return [0, 0, 0] if r_mag < 1e-10  # Avoid division by zero
    
    # Calculate gravitational force
    force_mag = g * particle1[:mass] * particle2[:mass] / (r_mag ** 2)
    
    # Force vector (on particle1, pointing toward particle2)
    r.map { |component| force_mag * component / r_mag }
  end

  def coulomb_force(particle1, particle2, k = 8.99e9)
    return [0, 0, 0] if particle1[:charge] == 0 || particle2[:charge] == 0
    
    r1 = particle1[:position]
    r2 = particle2[:position]
    
    # Calculate distance vector
    r = r2.zip(r1).map { |x, y| x - y }
    r_mag = Math.sqrt(r.sum { |x| x ** 2 })
    
    return [0, 0, 0] if r_mag < 1e-10
    
    # Calculate Coulomb force
    force_mag = k * particle1[:charge] * particle2[:charge] / (r_mag ** 2)
    
    # Force vector
    r.map { |component| force_mag * component / r_mag }
  end

  def spring_force(particle, equilibrium_position, spring_constant)
    displacement = particle[:position].zip(equilibrium_position).map { |pos, eq| pos - eq }
    
    # F = -kx
    displacement.map { |disp| -spring_constant * disp }
  end

  def drag_force(particle, drag_coefficient)
    # F = -bv (linear drag)
    particle[:velocity].map { |vel| -drag_coefficient * vel }
  end

  def kinetic_energy(particle)
    v_squared = particle[:velocity].sum { |v| v ** 2 }
    0.5 * particle[:mass] * v_squared
  end

  def potential_energy()
    pe = 0
    
    # Gravitational potential energy
    @particles.combination(2).each do |p1, p2|
      r = p2[:position].zip(p1[:position]).map { |x, y| x - y }
      r_mag = Math.sqrt(r.sum { |x| x ** 2 })
      
      next if r_mag < 1e-10
      
      pe -= 6.67430e-11 * p1[:mass] * p2[:mass] / r_mag
    end
    
    pe
  end

  def total_energy()
    ke = @particles.sum { |particle| kinetic_energy(particle) }
    pe = potential_energy()
    ke + pe
  end

  def center_of_mass()
    total_mass = @particles.sum { |p| p[:mass] }
    
    return [0, 0, 0] if total_mass == 0
    
    com = @particles.map do |particle|
      particle[:position].map { |pos| pos * particle[:mass] }
    end.reduce do |sum, mass_pos|
      sum.zip(mass_pos).map { |s, mp| s + mp }
    end
    
    com.map { |component| component / total_mass }
  end

  def angular_momentum()
    com = center_of_mass()
    
    @particles.sum do |particle|
      # Position relative to center of mass
      r = particle[:position].zip(com).map { |pos, com_pos| pos - com_pos }
      
      # Angular momentum L = r × mv
      rx, ry, rz = r
      vx, vy, vz = particle[:velocity]
      m = particle[:mass]
      
      [
        m * (ry * vz - rz * vy),
        m * (rz * vx - rx * vz),
        m * (rx * vy - ry * vx)
      ]
    end.reduce do |sum, l|
      sum.zip(l).map { |s, li| s + li }
    end
  end

  def get_trajectory(particle_index)
    # This would need to store position history during simulation
    puts "Trajectory tracking not implemented in this simplified version"
    []
  end

  def export_state(filename)
    state = {
      time: @time,
      particles: @particles,
      total_energy: total_energy(),
      center_of_mass: center_of_mass(),
      angular_momentum: angular_momentum()
    }
    
    File.write(filename, JSON.pretty_generate(state))
    puts "State exported to #{filename}"
  end

  private

  def calculate_forces
    # Reset forces
    @particles.each { |particle| particle[:force] = [0, 0, 0] }
    
    # Apply forces
    @forces.each do |force|
      case force[:type]
      when :gravity
        apply_gravitational_forces()
      when :coulomb
        apply_coulomb_forces()
      when :spring
        apply_spring_forces(force[:params])
      when :drag
        apply_drag_forces(force[:params])
      end
    end
  end

  def apply_gravitational_forces
    @particles.combination(2).each do |p1, p2|
      force = gravitational_force(p1, p2)
      
      # Newton's third law
      p1[:force] = p1[:force].zip(force).map { |f1, f| f1 + f }
      p2[:force] = p2[:force].zip(force).map { |f2, f| f2 - f }
    end
  end

  def apply_coulomb_forces
    @particles.combination(2).each do |p1, p2|
      force = coulomb_force(p1, p2)
      
      # Newton's third law
      p1[:force] = p1[:force].zip(force).map { |f1, f| f1 + f }
      p2[:force] = p2[:force].zip(force).map { |f2, f| f2 - f }
    end
  end

  def apply_spring_forces(params)
    equilibrium_position = params[:equilibrium_position] || [0, 0, 0]
    spring_constant = params[:spring_constant] || 1.0
    
    @particles.each do |particle|
      force = spring_force(particle, equilibrium_position, spring_constant)
      particle[:force] = particle[:force].zip(force).map { |f1, f| f1 + f }
    end
  end

  def apply_drag_forces(params)
    drag_coefficient = params[:drag_coefficient] || 0.1
    
    @particles.each do |particle|
      force = drag_force(particle, drag_coefficient)
      particle[:force] = particle[:force].zip(force).map { |f1, f| f1 + f }
    end
  end
end
```

## Best Practices

1. **Numerical Stability**: Use appropriate numerical methods and check for convergence
2. **Error Analysis**: Always estimate and report numerical errors
3. **Validation**: Compare results with analytical solutions when available
4. **Performance**: Optimize algorithms for large-scale problems
5. **Documentation**: Document mathematical methods and assumptions
6. **Testing**: Verify implementations with known test cases
7. **Visualization**: Use appropriate plots to understand results

## Conclusion

Ruby provides a solid foundation for scientific computing, from numerical methods to physics simulations. While specialized scientific languages like Python or MATLAB have more extensive libraries, Ruby's elegance and flexibility make it suitable for many scientific computing tasks, especially when integration with web applications is required.

## Further Reading

- [Numerical Recipes](https://numerical.recipes/)
- [Scientific Computing with Ruby](https://github.com/SciRuby)
- [Computational Physics](https://www.compphys.org/)
- [Numerical Analysis](https://en.wikipedia.org/wiki/Numerical_analysis)
