# Advanced Artificial Intelligence in Ruby

## Overview

This guide explores cutting-edge AI and machine learning concepts implemented in Ruby. While Python dominates the AI landscape, Ruby provides unique advantages for certain AI applications, particularly in web integration, rapid prototyping, and enterprise environments.

## Neural Networks from Scratch

### Deep Neural Network Architecture

```ruby
class NeuralNetwork
  attr_reader :layers, :weights, :biases, :learning_rate, :activation_functions
  
  def initialize(layer_sizes, learning_rate = 0.01)
    @layer_sizes = layer_sizes
    @learning_rate = learning_rate
    @layers = layer_sizes.length - 1
    @weights = []
    @biases = []
    @activation_functions = []
    
    # Initialize weights and biases
    @layers.times do |i|
      # Xavier initialization
      input_size = layer_sizes[i]
      output_size = layer_sizes[i + 1]
      
      @weights << Matrix.build(output_size, input_size) { rand * Math.sqrt(2.0 / input_size) }
      @biases << Matrix.build(output_size, 1) { 0.0 }
      @activation_functions << (i < @layers - 1 ? :relu : :softmax)
    end
  end
  
  def forward(input)
    @activations = [input]
    @z_values = []
    
    @layers.times do |i|
      z = @weights[i] * @activations.last + @biases[i]
      @z_values << z
      
      activation = apply_activation(z, @activation_functions[i])
      @activations << activation
    end
    
    @activations.last
  end
  
  def backward(target)
    m = target.column_count
    
    # Calculate output layer error
    output_error = @activations.last - target
    output_delta = output_error.elementwise_product(
      activation_derivative(@z_values.last, @activation_functions.last)
    )
    
    # Backpropagate through layers
    @deltas = [output_delta]
    
    (@layers - 1).downto(0) do |i|
      if i > 0
        error = @weights[i].transpose * @deltas.first
        delta = error.elementwise_product(
          activation_derivative(@z_values[i - 1], @activation_functions[i - 1])
        )
        @deltas.unshift(delta)
      end
    end
    
    # Update weights and biases
    @layers.times do |i|
      grad_w = @deltas[i] * @activations[i].transpose
      grad_b = @deltas[i]
      
      @weights[i] -= grad_w * (@learning_rate / m)
      @biases[i] -= grad_b * (@learning_rate / m)
    end
    
    # Calculate loss
    loss = calculate_loss(@activations.last, target)
    loss
  end
  
  def train(x_data, y_data, epochs = 1000, batch_size = 32)
    losses = []
    
    epochs.times do |epoch|
      total_loss = 0.0
      batches = create_batches(x_data, y_data, batch_size)
      
      batches.each do |batch_x, batch_y|
        batch_loss = 0.0
        
        batch_x.column_vectors.each_with_index do |input, i|
          target = batch_y.column_vectors[i]
          
          forward(input)
          batch_loss += backward(target)
        end
        
        total_loss += batch_loss
      end
      
      avg_loss = total_loss / x_data.column_count
      losses << avg_loss
      
      puts "Epoch #{epoch + 1}/#{epochs}, Loss: #{avg_loss.round(6)}" if (epoch + 1) % 100 == 0
    end
    
    losses
  end
  
  def predict(input)
    output = forward(input)
    
    case @activation_functions.last
    when :softmax
      max_index = output.column_vectors.first.each_with_index.max_by { |val, i| val }[1]
      [0.0] * output.row_count.tap { |arr| arr[max_index] = 1.0 }
    when :sigmoid
      output.map { |val| val > 0.5 ? 1.0 : 0.0 }
    else
      output
    end
  end
  
  def save_model(filename)
    model_data = {
      layer_sizes: @layer_sizes,
      learning_rate: @learning_rate,
      weights: @weights.map(&:to_a),
      biases: @biases.map(&:to_a),
      activation_functions: @activation_functions
    }
    
    File.write(filename, Marshal.dump(model_data))
  end
  
  def self.load_model(filename)
    model_data = Marshal.load(File.read(filename))
    
    network = new(model_data[:layer_sizes], model_data[:learning_rate])
    
    network.instance_variable_set(:@weights, 
      model_data[:weights].map { |w| Matrix[*w] })
    network.instance_variable_set(:@biases, 
      model_data[:biases].map { |b| Matrix[*b] })
    network.instance_variable_set(:@activation_functions, 
      model_data[:activation_functions])
    
    network
  end
  
  private
  
  def apply_activation(matrix, function)
    case function
    when :relu
      matrix.map { |x| [0, x].max }
    when :sigmoid
      matrix.map { |x| 1.0 / (1.0 + Math.exp(-x)) }
    when :tanh
      matrix.map { |x| Math.tanh(x) }
    when :softmax
      exp_values = matrix.map { |x| Math.exp(x) }
      sum_exp = exp_values.sum
      exp_values.map { |x| x / sum_exp }
    when :leaky_relu
      matrix.map { |x| x > 0 ? x : 0.01 * x }
    else
      matrix
    end
  end
  
  def activation_derivative(matrix, function)
    case function
    when :relu
      matrix.map { |x| x > 0 ? 1.0 : 0.0 }
    when :sigmoid
      sigmoid = matrix.map { |x| 1.0 / (1.0 + Math.exp(-x)) }
      sigmoid.elementwise_product(Matrix.build(sigmoid.row_count, sigmoid.column_count) { 1.0 } - sigmoid)
    when :tanh
      tanh = matrix.map { |x| Math.tanh(x) }
      Matrix.build(tanh.row_count, tanh.column_count) { 1.0 } - tanh.elementwise_product(tanh)
    when :softmax
      # Simplified softmax derivative (for cross-entropy loss)
      Matrix.build(matrix.row_count, matrix.column_count) { 1.0 }
    when :leaky_relu
      matrix.map { |x| x > 0 ? 1.0 : 0.01 }
    else
      Matrix.build(matrix.row_count, matrix.column_count) { 1.0 }
    end
  end
  
  def calculate_loss(output, target)
    case @activation_functions.last
    when :softmax
      # Cross-entropy loss
      -(target.elementwise_product(output.map { |x| Math.log(x + 1e-10) })).sum
    when :sigmoid
      # Binary cross-entropy loss
      -(target.elementwise_product(output.map { |x| Math.log(x + 1e-10) }) +
        (Matrix.build(target.row_count, target.column_count) { 1.0 } - target)
        .elementwise_product((Matrix.build(output.row_count, output.column_count) { 1.0 } - output)
        .map { |x| Math.log(x + 1e-10) })).sum
    else
      # Mean squared error
      ((output - target).elementwise_product(output - target)).sum / (2.0 * target.column_count)
    end
  end
  
  def create_batches(x_data, y_data, batch_size)
    batches = []
    total_samples = x_data.column_count
    
    (0...total_samples).step(batch_size) do |start|
      end_idx = [start + batch_size, total_samples].min
      
      batch_x = Matrix.build(x_data.row_count, end_idx - start) do |i, j|
        x_data[i, start + j]
      end
      
      batch_y = Matrix.build(y_data.row_count, end_idx - start) do |i, j|
        y_data[i, start + j]
      end
      
      batches << [batch_x, batch_y]
    end
    
    batches
  end
end

# Matrix class for neural network operations
class Matrix
  def self.build(rows, cols, &block)
    Matrix.new(Array.new(rows) { Array.new(cols) { |j| yield(rows, j) } })
  end
  
  def initialize(data)
    @data = data
  end
  
  def [](i, j)
    @data[i][j]
  end
  
  def []=(i, j, value)
    @data[i][j] = value
  end
  
  def row_count
    @data.length
  end
  
  def column_count
    @data.first.length
  end
  
  def +(other)
    result = Array.new(row_count) { Array.new(column_count) }
    row_count.times do |i|
      column_count.times do |j|
        result[i][j] = self[i, j] + other[i, j]
      end
    end
    Matrix.new(result)
  end
  
  def -(other)
    result = Array.new(row_count) { Array.new(column_count) }
    row_count.times do |i|
      column_count.times do |j|
        result[i][j] = self[i, j] - other[i, j]
      end
    end
    Matrix.new(result)
  end
  
  def *(scalar)
    result = Array.new(row_count) { Array.new(column_count) }
    row_count.times do |i|
      column_count.times do |j|
        result[i][j] = self[i, j] * scalar
      end
    end
    Matrix.new(result)
  end
  
  def multiply(other)
    result = Array.new(row_count) { Array.new(other.column_count) }
    row_count.times do |i|
      other.column_count.times do |j|
        sum = 0.0
        column_count.times do |k|
          sum += self[i, k] * other[k, j]
        end
        result[i][j] = sum
      end
    end
    Matrix.new(result)
  end
  
  def elementwise_product(other)
    result = Array.new(row_count) { Array.new(column_count) }
    row_count.times do |i|
      column_count.times do |j|
        result[i][j] = self[i, j] * other[i, j]
      end
    end
    Matrix.new(result)
  end
  
  def transpose
    result = Array.new(column_count) { Array.new(row_count) }
    column_count.times do |i|
      row_count.times do |j|
        result[i][j] = self[j, i]
      end
    end
    Matrix.new(result)
  end
  
  def map(&block)
    result = Array.new(row_count) { Array.new(column_count) }
    row_count.times do |i|
      column_count.times do |j|
        result[i][j] = yield(self[i, j])
      end
    end
    Matrix.new(result)
  end
  
  def sum
    @data.flatten.sum
  end
  
  def column_vectors
    (0...column_count).map { |j| Matrix.new(@data.map { |row| [row[j]] }) }
  end
  
  def to_a
    @data
  end
end
```

### Advanced Neural Network Architectures

```ruby
class ConvolutionalNeuralNetwork
  def initialize(input_shape, conv_layers, fc_layers, num_classes)
    @input_shape = input_shape
    @conv_layers = conv_layers
    @fc_layers = fc_layers
    @num_classes = num_classes
    
    @conv_weights = []
    @conv_biases = []
    @pool_layers = []
    
    # Initialize convolutional layers
    conv_layers.each do |layer|
      filters, kernel_size, stride, padding = layer
      
      @conv_weights << initialize_conv_weights(kernel_size, input_shape.last, filters)
      @conv_biases << Array.new(filters) { 0.0 }
      @pool_layers << { type: :max, size: 2, stride: 2 }
      
      # Update input shape for next layer
      output_size = calculate_conv_output(input_shape, kernel_size, stride, padding)
      input_shape = [output_size[0], output_size[1], filters]
    end
    
    # Calculate flattened size for fully connected layers
    @flattened_size = input_shape[0] * input_shape[1] * input_shape[2]
    
    # Initialize fully connected layers
    fc_sizes = [@flattened_size] + fc_layers + [num_classes]
    @fc_weights = []
    @fc_biases = []
    
    fc_sizes.each_cons(2) do |input_size, output_size|
      @fc_weights << initialize_fc_weights(input_size, output_size)
      @fc_biases << Array.new(output_size) { 0.0 }
    end
  end
  
  def forward(input)
    @conv_outputs = []
    current_input = input
    
    # Forward through convolutional layers
    @conv_layers.each_with_index do |layer, i|
      filters, kernel_size, stride, padding = layer
      
      # Convolution
      conv_output = convolution(current_input, @conv_weights[i], @conv_biases[i], stride, padding)
      @conv_outputs << conv_output
      
      # Activation (ReLU)
      conv_output = relu_activation(conv_output)
      
      # Pooling
      conv_output = max_pooling(conv_output, @pool_layers[i][:size], @pool_layers[i][:stride])
      
      current_input = conv_output
    end
    
    # Flatten for fully connected layers
    flattened = flatten(current_input)
    @fc_outputs = [flattened]
    
    # Forward through fully connected layers
    @fc_weights.each_with_index do |weights, i|
      fc_output = matrix_multiply(@fc_outputs.last, weights)
      fc_output = vector_add(fc_output, @fc_biases[i])
      
      if i < @fc_weights.length - 1
        fc_output = relu_activation(fc_output)
      else
        fc_output = softmax_activation(fc_output)
      end
      
      @fc_outputs << fc_output
    end
    
    @fc_outputs.last
  end
  
  def backward(target, learning_rate = 0.01)
    # Backward through fully connected layers
    fc_deltas = []
    
    # Output layer delta
    output_delta = @fc_outputs.last - target
    fc_deltas.unshift(output_delta)
    
    # Hidden layer deltas
    (@fc_weights.length - 1).downto(1) do |i|
      delta = matrix_multiply(fc_deltas.first, @fc_weights[i].transpose)
      delta = relu_derivative(@fc_outputs[i], delta)
      fc_deltas.unshift(delta)
    end
    
    # Update fully connected weights and biases
    @fc_weights.each_with_index do |weights, i|
      grad_w = matrix_multiply(@fc_outputs[i].transpose, fc_deltas[i])
      grad_b = fc_deltas[i]
      
      @fc_weights[i] = matrix_subtract(weights, grad_w * learning_rate)
      @fc_biases[i] = vector_subtract(@fc_biases[i], grad_b * learning_rate)
    end
    
    # Backward through convolutional layers (simplified)
    conv_deltas = []
    
    # Convert FC delta to conv delta
    fc_to_conv_delta = fc_deltas.last
    reshaped_delta = reshape_to_conv(fc_to_conv_delta, @conv_outputs.last.shape)
    conv_deltas.unshift(reshaped_delta)
    
    # Backprop through conv layers
    (@conv_layers.length - 1).downto(1) do |i|
      # Simplified conv backprop
      delta = convolution_backward(conv_deltas.first, @conv_weights[i])
      delta = relu_derivative(@conv_outputs[i], delta)
      conv_deltas.unshift(delta)
    end
    
    # Update convolutional weights and biases
    @conv_weights.each_with_index do |weights, i|
      grad_w = convolution_gradient(@conv_outputs[i], conv_deltas[i])
      grad_b = conv_deltas[i].sum(axis: [0, 1])
      
      @conv_weights[i] = matrix_subtract(weights, grad_w * learning_rate)
      @conv_biases[i] = vector_subtract(@conv_biases[i], grad_b * learning_rate)
    end
  end
  
  def train(x_data, y_data, epochs = 100, batch_size = 32)
    losses = []
    
    epochs.times do |epoch|
      total_loss = 0.0
      batches = create_image_batches(x_data, y_data, batch_size)
      
      batches.each do |batch_x, batch_y|
        batch_loss = 0.0
        
        batch_x.each_with_index do |input, i|
          target = batch_y[i]
          
          output = forward(input)
          batch_loss += cross_entropy_loss(output, target)
          backward(target)
        end
        
        total_loss += batch_loss
      end
      
      avg_loss = total_loss / x_data.length
      losses << avg_loss
      
      puts "Epoch #{epoch + 1}/#{epochs}, Loss: #{avg_loss.round(6)}" if (epoch + 1) % 10 == 0
    end
    
    losses
  end
  
  def predict(input)
    output = forward(input)
    output.each_with_index.max_by { |val, i| val }[1]
  end
  
  private
  
  def initialize_conv_weights(kernel_size, input_channels, output_channels)
    # He initialization for ReLU
    Array.new(output_channels) do
      Array.new(input_channels) do
        Array.new(kernel_size) do
          Array.new(kernel_size) { rand * Math.sqrt(2.0 / (kernel_size * kernel_size * input_channels)) }
        end
      end
    end
  end
  
  def initialize_fc_weights(input_size, output_size)
    Array.new(input_size) { Array.new(output_size) { rand * Math.sqrt(2.0 / input_size) } }
  end
  
  def convolution(input, weights, biases, stride, padding)
    # Simplified convolution implementation
    input_height, input_width, input_channels = input.shape
    kernel_size = weights[0][0].length
    num_filters = weights.length
    
    output_height = (input_height + 2 * padding - kernel_size) / stride + 1
    output_width = (input_width + 2 * padding - kernel_size) / stride + 1
    
    output = Array.new(output_height) do
      Array.new(output_width) do
        Array.new(num_filters) { 0.0 }
      end
    end
    
    num_filters.times do |f|
      input_channels.times do |c|
        output_height.times do |i|
          output_width.times do |j|
            sum = 0.0
            
            kernel_size.times do |ki|
              kernel_size.times do |kj|
                input_i = i * stride + ki - padding
                input_j = j * stride + kj - padding
                
                if input_i >= 0 && input_i < input_height && 
                   input_j >= 0 && input_j < input_width
                  sum += input[input_i][input_j][c] * weights[f][c][ki][kj]
                end
              end
            end
            
            output[i][j][f] += sum + biases[f]
          end
        end
      end
    end
    
    output
  end
  
  def max_pooling(input, pool_size, stride)
    height, width, channels = input.shape
    output_height = (height - pool_size) / stride + 1
    output_width = (width - pool_size) / stride + 1
    
    output = Array.new(output_height) do
      Array.new(output_width) do
        Array.new(channels) { 0.0 }
      end
    end
    
    output_height.times do |i|
      output_width.times do |j|
        channels.times do |c|
          max_val = -Float::INFINITY
          
          pool_size.times do |pi|
            pool_size.times do |pj|
              input_i = i * stride + pi
              input_j = j * stride + pj
              
              if input_i < height && input_j < width
                max_val = [max_val, input[input_i][input_j][c]].max
              end
            end
          end
          
          output[i][j][c] = max_val
        end
      end
    end
    
    output
  end
  
  def flatten(input)
    height, width, channels = input.shape
    input.flatten
  end
  
  def reshape_to_conv(vector, target_shape)
    # Simplified reshape
    Array.new(target_shape[0]) do |i|
      Array.new(target_shape[1]) do |j|
        Array.new(target_shape[2]) do |k|
          idx = i * target_shape[1] * target_shape[2] + j * target_shape[2] + k
          vector[idx]
        end
      end
    end
  end
  
  def calculate_conv_output(input_shape, kernel_size, stride, padding)
    height = (input_shape[0] + 2 * padding - kernel_size) / stride + 1
    width = (input_shape[1] + 2 * padding - kernel_size) / stride + 1
    [height, width]
  end
  
  def relu_activation(input)
    if input.is_a?(Array)
      input.map { |x| [0, x].max }
    else
      [0, input].max
    end
  end
  
  def softmax_activation(input)
    exp_values = input.map { |x| Math.exp(x) }
    sum_exp = exp_values.sum
    exp_values.map { |x| x / sum_exp }
  end
  
  def relu_derivative(input, delta)
    input.map { |x| x > 0 ? delta : 0.0 }
  end
  
  def cross_entropy_loss(output, target)
    -(target * output.map { |x| Math.log(x + 1e-10) }).sum
  end
  
  def matrix_multiply(a, b)
    Array.new(a.length) do |i|
      Array.new(b.first.length) do |j|
        a[i].each_with_index.sum { |val, k| val * b[k][j] }
      end
    end
  end
  
  def matrix_subtract(a, b)
    Array.new(a.length) do |i|
      Array.new(a.first.length) do |j|
        a[i][j] - b[i][j]
      end
    end
  end
  
  def vector_add(a, b)
    a.each_with_index.map { |val, i| val + b[i] }
  end
  
  def vector_subtract(a, b)
    a.each_with_index.map { |val, i| val - b[i] }
  end
end

class RecurrentNeuralNetwork
  def initialize(input_size, hidden_size, output_size, learning_rate = 0.01)
    @input_size = input_size
    @hidden_size = hidden_size
    @output_size = output_size
    @learning_rate = learning_rate
    
    # Initialize weights
    @wxh = Array.new(hidden_size) { Array.new(input_size) { rand * 0.01 } }
    @whh = Array.new(hidden_size) { Array.new(hidden_size) { rand * 0.01 } }
    @why = Array.new(output_size) { Array.new(hidden_size) { rand * 0.01 } }
    
    # Initialize biases
    @bh = Array.new(hidden_size) { 0.0 }
    @by = Array.new(output_size) { 0.0 }
  end
  
  def forward(inputs)
    @hidden_states = []
    @outputs = []
    
    hidden_state = Array.new(@hidden_size) { 0.0 }
    
    inputs.each do |input|
      # Calculate hidden state
      xh = matrix_vector_multiply(@wxh, input)
      hh = matrix_vector_multiply(@whh, hidden_state)
      hidden_state = tanh_activation(vector_add(vector_add(xh, hh), @bh))
      
      # Calculate output
      y = matrix_vector_multiply(@why, hidden_state)
      output = softmax_activation(vector_add(y, @by))
      
      @hidden_states << hidden_state.dup
      @outputs << output
    end
    
    @outputs
  end
  
  def backward(inputs, targets)
    # Initialize gradients
    dwxh = Array.new(@hidden_size) { Array.new(@input_size) { 0.0 } }
    dwhh = Array.new(@hidden_size) { Array.new(@hidden_size) { 0.0 } }
    dwhy = Array.new(@output_size) { Array.new(@hidden_size) { 0.0 } }
    dbh = Array.new(@hidden_size) { 0.0 }
    dby = Array.new(@output_size) { 0.0 }
    
    # Initialize previous hidden state gradient
    dh_next = Array.new(@hidden_size) { 0.0 }
    
    # Backward through time
    inputs.each_with_index.reverse_each do |input, t|
      target = targets[t]
      output = @outputs[t]
      hidden_state = @hidden_states[t]
      
      # Output layer gradients
      dy = output.dup
      target.each_with_index { |target_val, i| dy[i] -= target_val }
      
      dwhy = matrix_add(dwhy, outer_product(dy, hidden_state))
      dby = vector_add(dby, dy)
      
      # Hidden layer gradients
      dh = matrix_vector_multiply(@why.transpose, dy)
      dh = vector_add(dh, dh_next)
      dhraw = tanh_derivative(hidden_state, dh)
      
      dwhh = matrix_add(dwhh, outer_product(dhraw, @hidden_states[t - 1] || Array.new(@hidden_size) { 0.0 }))
      dbh = vector_add(dbh, dhraw)
      
      if t > 0
        dwxh = matrix_add(dwxh, outer_product(dhraw, inputs[t - 1]))
      end
      
      dh_next = matrix_vector_multiply(@whh.transpose, dhraw)
    end
    
    # Clip gradients to prevent exploding gradients
    clip_gradients(dwxh, dwhh, dwhy, dbh, dby)
    
    # Update weights and biases
    @wxh = matrix_subtract(@wxh, dwxh * @learning_rate)
    @whh = matrix_subtract(@whh, dwhh * @learning_rate)
    @why = matrix_subtract(@why, dwhy * @learning_rate)
    @bh = vector_subtract(@bh, dbh * @learning_rate)
    @by = vector_subtract(@by, dby * @learning_rate)
  end
  
  def train(inputs, targets, epochs = 100)
    losses = []
    
    epochs.times do |epoch|
      forward(inputs)
      backward(inputs, targets)
      
      loss = calculate_loss(@outputs, targets)
      losses << loss
      
      puts "Epoch #{epoch + 1}/#{epochs}, Loss: #{loss.round(6)}" if (epoch + 1) % 10 == 0
    end
    
    losses
  end
  
  def generate(seed_input, length)
    generated = []
    hidden_state = Array.new(@hidden_size) { 0.0 }
    
    current_input = seed_input
    
    length.times do
      # Forward pass
      xh = matrix_vector_multiply(@wxh, current_input)
      hh = matrix_vector_multiply(@whh, hidden_state)
      hidden_state = tanh_activation(vector_add(vector_add(xh, hh), @bh))
      
      y = matrix_vector_multiply(@why, hidden_state)
      output = softmax_activation(vector_add(y, @by))
      
      # Sample from output distribution
      next_input = sample_from_distribution(output)
      generated << next_input
      
      current_input = one_hot_encode(next_input, @input_size)
    end
    
    generated
  end
  
  private
  
  def tanh_activation(x)
    if x.is_a?(Array)
      x.map { |val| Math.tanh(val) }
    else
      Math.tanh(x)
    end
  end
  
  def tanh_derivative(tanh_output, grad_output)
    tanh_output.each_with_index.map { |val, i| grad_output[i] * (1.0 - val * val) }
  end
  
  def softmax_activation(x)
    exp_values = x.map { |val| Math.exp(val) }
    sum_exp = exp_values.sum
    exp_values.map { |val| val / sum_exp }
  end
  
  def matrix_vector_multiply(matrix, vector)
    matrix.map { |row| row.each_with_index.sum { |val, i| val * vector[i] } }
  end
  
  def vector_add(a, b)
    a.each_with_index.map { |val, i| val + b[i] }
  end
  
  def vector_subtract(a, b)
    a.each_with_index.map { |val, i| val - b[i] }
  end
  
  def matrix_add(a, b)
    a.each_with_index.map { |row, i| row.each_with_index.map { |val, j| val + b[i][j] } }
  end
  
  def matrix_subtract(a, b)
    a.each_with_index.map { |row, i| row.each_with_index.map { |val, j| val - b[i][j] } }
  end
  
  def outer_product(a, b)
    a.map { |val_a| b.map { |val_b| val_a * val_b } }
  end
  
  def calculate_loss(outputs, targets)
    total_loss = 0.0
    
    outputs.each_with_index do |output, t|
      target = targets[t]
      total_loss -= target.each_with_index.sum { |target_val, i| target_val * Math.log(output[i] + 1e-10) }
    end
    
    total_loss / outputs.length
  end
  
  def clip_gradients(*gradients)
    clip_value = 5.0
    
    gradients.each do |grad|
      grad.each_with_index do |row, i|
        if row.is_a?(Array)
          row.each_with_index do |val, j|
            grad[i][j] = [[-clip_value, val].max, clip_value].min
          end
        else
          grad[i] = [[-clip_value, row].max, clip_value].min
        end
      end
    end
  end
  
  def sample_from_distribution(probabilities)
    random_val = rand
    cumulative_prob = 0.0
    
    probabilities.each_with_index do |prob, i|
      cumulative_prob += prob
      return i if random_val <= cumulative_prob
    end
    
    probabilities.length - 1
  end
  
  def one_hot_encode(index, size)
    encoded = Array.new(size) { 0.0 }
    encoded[index] = 1.0
    encoded
  end
end
```

## Natural Language Processing

### Advanced NLP Pipeline

```ruby
class NaturalLanguageProcessor
  def initialize
    @vocabulary = {}
    @word_embeddings = {}
    @models = {}
    @preprocessors = {}
  end
  
  def build_vocabulary(corpus, min_frequency = 5)
    word_counts = Hash.new(0)
    
    corpus.each do |text|
      tokens = tokenize(text)
      tokens.each { |token| word_counts[token] += 1 }
    end
    
    # Filter by minimum frequency
    filtered_words = word_counts.select { |_, count| count >= min_frequency }
    
    # Build vocabulary
    @vocabulary = {}
    filtered_words.keys.each_with_index do |word, i|
      @vocabulary[word] = i
    end
    
    # Add special tokens
    @vocabulary['<PAD>'] = @vocabulary.length
    @vocabulary['<UNK>'] = @vocabulary.length
    @vocabulary['<START>'] = @vocabulary.length
    @vocabulary['<END>'] = @vocabulary.length
    
    puts "Vocabulary built with #{@vocabulary.length} words"
    @vocabulary
  end
  
  def load_word_embeddings(embedding_file)
    File.readlines(embedding_file).each do |line|
      parts = line.strip.split
      word = parts[0]
      vector = parts[1..-1].map(&:to_f)
      @word_embeddings[word] = vector
    end
    
    puts "Loaded #{@word_embeddings.length} word embeddings"
  end
  
  def create_embedding_matrix(embedding_dim = 300)
    vocab_size = @vocabulary.length
    @embedding_matrix = Array.new(vocab_size) { Array.new(embedding_dim) { rand * 0.01 } }
    
    @vocabulary.each do |word, index|
      if @word_embeddings[word]
        @embedding_matrix[index] = @word_embeddings[word]
      end
    end
    
    @embedding_matrix
  end
  
  def tokenize(text)
    # Advanced tokenization
    text = text.downcase
    text = text.gsub(/[^\w\s]/, ' ')  # Remove punctuation
    text = text.gsub(/\s+/, ' ')       # Normalize whitespace
    
    tokens = text.split(' ')
    tokens.reject(&:empty?)
  end
  
  def text_to_sequence(text, max_length = 100)
    tokens = tokenize(text)
    sequence = tokens.map { |token| @vocabulary[token] || @vocabulary['<UNK>'] }
    
    # Pad or truncate sequence
    if sequence.length > max_length
      sequence[0...max_length]
    else
      sequence + Array.new(max_length - sequence.length) { @vocabulary['<PAD>'] }
    end
  end
  
  def sequence_to_text(sequence)
    tokens = sequence.map do |token_id|
      @vocabulary.key(token_id) || '<UNK>'
    end
    
    tokens.reject { |token| ['<PAD>', '<START>', '<END>'].include?(token) }.join(' ')
  end
  
  def train_word2vec(corpus, embedding_dim = 300, window_size = 5, learning_rate = 0.025)
    vocab_size = @vocabulary.length
    
    # Initialize weights
    @W1 = Array.new(vocab_size) { Array.new(embedding_dim) { rand * 0.01 } }
    @W2 = Array.new(embedding_dim) { Array.new(vocab_size) { rand * 0.01 } }
    
    epochs = 10
    
    epochs.times do |epoch|
      total_loss = 0.0
      
      corpus.each do |text|
        tokens = tokenize(text)
        tokens.each_with_index do |target_word, i|
          # Context window
          start_idx = [0, i - window_size].max
          end_idx = [tokens.length - 1, i + window_size].min
          
          context_words = tokens[start_idx...i] + tokens[i + 1..end_idx]
          
          context_words.each do |context_word|
            # Skip unknown words
            next unless @vocabulary[target_word] && @vocabulary[context_word]
            
            target_idx = @vocabulary[target_word]
            context_idx = @vocabulary[context_word]
            
            # Forward pass
            h = @W1[target_idx]
            u = matrix_vector_multiply(@W2.transpose, h)
            y = softmax_activation(u)
            
            # Calculate loss
            loss = -Math.log(y[context_idx] + 1e-10)
            total_loss += loss
            
            # Backward pass
            e = y.dup
            e[context_idx] -= 1
            
            # Update weights
            @W1[target_idx] = vector_subtract(@W1[target_idx], 
              matrix_vector_multiply(@W2, e) * learning_rate)
            
            @W2.each_with_index do |row, j|
              @W2[j] = vector_subtract(@W2[j], 
                [h[j] * e[j]] * vocab_size * learning_rate)
            end
          end
        end
      end
      
      avg_loss = total_loss / corpus.sum { |text| tokenize(text).length }
      puts "Epoch #{epoch + 1}/#{epochs}, Loss: #{avg_loss.round(6)}"
    end
    
    # Combine weights for final embeddings
    @word2vec_embeddings = {}
    @vocabulary.each do |word, index|
      @word2vec_embeddings[word] = vector_add(@W1[index], 
        matrix_vector_multiply(@W2.transpose, @W1[index]))
    end
  end
  
  def train_transformer(corpus, d_model = 512, num_heads = 8, num_layers = 6)
    # Simplified Transformer implementation
    @transformer = TransformerModel.new(
      vocab_size: @vocabulary.length,
      d_model: d_model,
      num_heads: num_heads,
      num_layers: num_layers
    )
    
    # Prepare training data
    input_sequences = corpus.map { |text| text_to_sequence(text) }
    target_sequences = input_sequences.map { |seq| seq[1..-1] + [@vocabulary['<END>']] }
    
    # Train transformer
    @transformer.train(input_sequences, target_sequences, epochs = 20)
  end
  
  def generate_text(prompt, max_length = 100, temperature = 1.0)
    input_sequence = text_to_sequence(prompt)
    generated_sequence = input_sequence.dup
    
    max_length.times do
      # Get next token probabilities
      next_token_probs = @transformer.predict_next_token(generated_sequence)
      
      # Apply temperature
      next_token_probs = next_token_probs.map { |prob| prob ** (1.0 / temperature) }
      sum_probs = next_token_probs.sum
      next_token_probs = next_token_probs.map { |prob| prob / sum_probs }
      
      # Sample next token
      next_token = sample_from_distribution(next_token_probs)
      
      # Add to sequence
      generated_sequence << next_token
      
      # Stop if end token
      break if next_token == @vocabulary['<END>']
      
      # Limit sequence length
      generated_sequence = generated_sequence[-100..-1] if generated_sequence.length > 100
    end
    
    sequence_to_text(generated_sequence)
  end
  
  def classify_text(text, classifier_type = :naive_bayes)
    case classifier_type
    when :naive_bayes
      naive_bayes_classify(text)
    when :svm
      svm_classify(text)
    when :neural
      neural_classify(text)
    else
      raise ArgumentError, "Unknown classifier type: #{classifier_type}"
    end
  end
  
  def extract_entities(text)
    # Named Entity Recognition (simplified)
    entities = []
    tokens = tokenize(text)
    
    # Simple pattern-based entity extraction
    tokens.each_with_index do |token, i|
      # Person names (simplified)
      if token.match?(/^[A-Z][a-z]+$/) && i > 0 && tokens[i-1].match?(/^[A-Z][a-z]+$/)
        entities << { type: :person, text: "#{tokens[i-1]} #{token}", start: i-1, end: i }
      end
      
      # Organizations (simplified)
      if token.match?(/^[A-Z][a-z]+(Inc|Corp|LLC|Ltd)$/)
        entities << { type: :organization, text: token, start: i, end: i }
      end
      
      # Locations (simplified)
      if token.match?(/^[A-Z][a-z]+$/) && i < tokens.length - 1 && 
         tokens[i+1].match?(/^(City|State|Country)$/)
        entities << { type: :location, text: "#{token} #{tokens[i+1]}", start: i, end: i+1 }
      end
    end
    
    entities
  end
  
  def summarize_text(text, max_sentences = 3)
    sentences = text.split(/[.!?]+/).reject(&:empty?)
    
    return text if sentences.length <= max_sentences
    
    # Extractive summarization using TF-IDF
    sentence_scores = sentences.map.with_index do |sentence, i|
      score = calculate_sentence_score(sentence)
      { sentence: sentence, score: score, index: i }
    end
    
    # Select top sentences
    top_sentences = sentence_scores.sort_by { |s| -s[:score] }[0...max_sentences]
    
    # Sort by original order
    top_sentences.sort_by { |s| s[:index] }.map { |s| s[:sentence] }.join('. ') + '.'
  end
  
  def translate_text(text, target_language)
    # Simplified translation using word embeddings
    tokens = tokenize(text)
    
    translated_tokens = tokens.map do |token|
      if @word_embeddings[token]
        # Find closest word in target language (simplified)
        find_closest_translation(token, target_language)
      else
        token  # Keep unknown words
      end
    end
    
    translated_tokens.join(' ')
  end
  
  private
  
  def calculate_sentence_score(sentence)
    tokens = tokenize(sentence)
    
    # TF-IDF score (simplified)
    tf = tokens.length.to_f
    idf = Math.log(@vocabulary.length.to_f / (tokens.length + 1))
    
    tf * idf
  end
  
  def find_closest_translation(word, target_language)
    # Simplified translation lookup
    translations = {
      'en' => { 'hello' => 'hello', 'world' => 'world' },
      'es' => { 'hello' => 'hola', 'world' => 'mundo' },
      'fr' => { 'hello' => 'bonjour', 'world' => 'monde' }
    }
    
    translations[target_language]&.dig(word) || word
  end
  
  def naive_bayes_classify(text)
    # Simplified Naive Bayes classification
    tokens = tokenize(text)
    
    # Calculate probabilities for each class
    class_probs = {}
    
    @models[:naive_bayes]&.each do |class_name, model|
      prob = Math.log(model[:prior])
      
      tokens.each do |token|
        token_prob = model[:likelihood][token] || 1e-10
        prob += Math.log(token_prob)
      end
      
      class_probs[class_name] = prob
    end
    
    class_probs.max_by { |_, prob| prob }&.first
  end
  
  def svm_classify(text)
    # Simplified SVM classification
    features = extract_features(text)
    
    @models[:svm]&.each do |class_name, model|
      score = features.zip(model[:weights]).sum { |f, w| f * w } + model[:bias]
      # Store score for comparison
    end
    
    # Return class with highest score
    class_probs.max_by { |_, prob| prob }&.first
  end
  
  def neural_classify(text)
    sequence = text_to_sequence(text)
    @models[:neural]&.predict(sequence)
  end
  
  def extract_features(text)
    # Extract features for classification
    tokens = tokenize(text)
    
    features = [
      tokens.length,                           # Text length
      tokens.count { |t| t.length > 6 },      # Long words
      tokens.count { |t| t.match?(/^[A-Z]/) }, # Capitalized words
      text.count(/[.!?]/),                    # Punctuation
      text.count(/\d/),                        # Numbers
    ]
    
    features
  end
end

class TransformerModel
  def initialize(vocab_size:, d_model:, num_heads:, num_layers:)
    @vocab_size = vocab_size
    @d_model = d_model
    @num_heads = num_heads
    @num_layers = num_layers
    
    # Initialize model components
    @embedding = EmbeddingLayer.new(vocab_size, d_model)
    @positional_encoding = PositionalEncoding.new(d_model)
    @encoder_layers = Array.new(num_layers) { EncoderLayer.new(d_model, num_heads) }
    @decoder_layers = Array.new(num_layers) { DecoderLayer.new(d_model, num_heads) }
    @output_layer = LinearLayer.new(d_model, vocab_size)
  end
  
  def train(input_sequences, target_sequences, epochs = 10, learning_rate = 0.001)
    epochs.times do |epoch|
      total_loss = 0.0
      
      input_sequences.each_with_index do |input_seq, i|
        target_seq = target_sequences[i]
        
        # Forward pass
        output = forward(input_seq, target_seq)
        
        # Calculate loss
        loss = calculate_loss(output, target_seq)
        total_loss += loss
        
        # Backward pass (simplified)
        backward(input_seq, target_seq, learning_rate)
      end
      
      avg_loss = total_loss / input_sequences.length
      puts "Epoch #{epoch + 1}/#{epochs}, Loss: #{avg_loss.round(6)}"
    end
  end
  
  def predict_next_token(sequence)
    # Simplified prediction
    encoded = @embedding.forward(sequence)
    encoded_with_pos = @positional_encoding.forward(encoded)
    
    # Pass through encoder layers
    @encoder_layers.each { |layer| encoded_with_pos = layer.forward(encoded_with_pos) }
    
    # Generate output probabilities
    output = @output_layer.forward(encoded_with_pos.last)
    softmax_activation(output)
  end
  
  private
  
  def forward(input_seq, target_seq)
    # Embedding and positional encoding
    input_embedded = @embedding.forward(input_seq)
    input_encoded = @positional_encoding.forward(input_embedded)
    
    target_embedded = @embedding.forward(target_seq)
    target_encoded = @positional_encoding.forward(target_embedded)
    
    # Encoder
    encoder_output = input_encoded
    @encoder_layers.each { |layer| encoder_output = layer.forward(encoder_output) }
    
    # Decoder
    decoder_output = target_encoded
    @decoder_layers.each_with_index do |layer, i|
      decoder_output = layer.forward(decoder_output, encoder_output)
    end
    
    # Output layer
    @output_layer.forward(decoder_output)
  end
  
  def calculate_loss(output, target)
    # Cross-entropy loss (simplified)
    output.each_with_index.sum do |output_row, i|
      target_idx = target[i]
      -Math.log(output_row[target_idx] + 1e-10)
    end
  end
  
  def backward(input_seq, target_seq, learning_rate)
    # Simplified backward pass
    # In practice, this would involve complex gradient calculations
  end
  
  def softmax_activation(x)
    exp_values = x.map { |val| Math.exp(val) }
    sum_exp = exp_values.sum
    exp_values.map { |val| val / sum_exp }
  end
end

class EmbeddingLayer
  def initialize(vocab_size, d_model)
    @vocab_size = vocab_size
    @d_model = d_model
    @weights = Array.new(vocab_size) { Array.new(d_model) { rand * 0.01 } }
  end
  
  def forward(input_ids)
    input_ids.map { |id| @weights[id] }
  end
end

class PositionalEncoding
  def initialize(d_model, max_length = 1000)
    @d_model = d_model
    @encoding = calculate_encoding(max_length)
  end
  
  def forward(embeddings)
    embeddings.map.with_index do |embedding, pos|
      embedding.map.with_index { |val, i| val + @encoding[pos][i] }
    end
  end
  
  private
  
  def calculate_encoding(max_length)
    Array.new(max_length) do |pos|
      Array.new(@d_model) do |i|
        if i.even?
          Math.sin(pos / (10000 ** (i / @d_model)))
        else
          Math.cos(pos / (10000 ** ((i - 1) / @d_model)))
        end
      end
    end
  end
end

class EncoderLayer
  def initialize(d_model, num_heads)
    @self_attention = MultiHeadAttention.new(d_model, num_heads)
    @feed_forward = FeedForwardNetwork.new(d_model)
    @norm1 = LayerNormalization.new(d_model)
    @norm2 = LayerNormalization.new(d_model)
  end
  
  def forward(input)
    # Self-attention with residual connection
    attn_output = @self_attention.forward(input, input, input)
    norm1_output = @norm1.forward(input + attn_output)
    
    # Feed-forward with residual connection
    ff_output = @feed_forward.forward(norm1_output)
    @norm2.forward(norm1_output + ff_output)
  end
end

class DecoderLayer
  def initialize(d_model, num_heads)
    @self_attention = MultiHeadAttention.new(d_model, num_heads)
    @cross_attention = MultiHeadAttention.new(d_model, num_heads)
    @feed_forward = FeedForwardNetwork.new(d_model)
    @norm1 = LayerNormalization.new(d_model)
    @norm2 = LayerNormalization.new(d_model)
    @norm3 = LayerNormalization.new(d_model)
  end
  
  def forward(input, encoder_output)
    # Self-attention with residual connection
    self_attn = @self_attention.forward(input, input, input)
    norm1 = @norm1.forward(input + self_attn)
    
    # Cross-attention with residual connection
    cross_attn = @cross_attention.forward(norm1, encoder_output, encoder_output)
    norm2 = @norm2.forward(norm1 + cross_attn)
    
    # Feed-forward with residual connection
    ff_output = @feed_forward.forward(norm2)
    @norm3.forward(norm2 + ff_output)
  end
end

class MultiHeadAttention
  def initialize(d_model, num_heads)
    @d_model = d_model
    @num_heads = num_heads
    @head_dim = d_model / num_heads
    
    # Initialize weight matrices
    @wq = Array.new(d_model) { Array.new(d_model) { rand * 0.01 } }
    @wk = Array.new(d_model) { Array.new(d_model) { rand * 0.01 } }
    @wv = Array.new(d_model) { Array.new(d_model) { rand * 0.01 } }
    @wo = Array.new(d_model) { Array.new(d_model) { rand * 0.01 } }
  end
  
  def forward(query, key, value)
    # Linear projections
    q = matrix_multiply(query, @wq)
    k = matrix_multiply(key, @wk)
    v = matrix_multiply(value, @wv)
    
    # Split into heads
    q_heads = split_into_heads(q)
    k_heads = split_into_heads(k)
    v_heads = split_into_heads(v)
    
    # Scaled dot-product attention for each head
    attention_outputs = q_heads.map.with_index do |q_head, i|
      scaled_dot_product_attention(q_head, k_heads[i], v_heads[i])
    end
    
    # Concatenate heads
    concatenated = concatenate_heads(attention_outputs)
    
    # Final linear projection
    matrix_multiply(concatenated, @wo)
  end
  
  private
  
  def scaled_dot_product_attention(query, key, value)
    # Calculate attention scores
    scores = matrix_multiply(query, matrix_transpose(key))
    scores = scores.map { |row| row.map { |score| score / Math.sqrt(@head_dim) } }
    
    # Apply softmax
    attention_weights = scores.map { |row| softmax(row) }
    
    # Apply attention weights to values
    matrix_multiply(attention_weights, value)
  end
  
  def split_into_heads(matrix)
    # Simplified head splitting
    Array.new(@num_heads) do |i|
      start_idx = i * @head_dim
      end_idx = start_idx + @head_dim
      matrix.map { |row| row[start_idx...end_idx] }
    end
  end
  
  def concatenate_heads(heads)
    # Simplified head concatenation
    heads.transpose.map { |group| group.flatten }
  end
  
  def softmax(x)
    exp_values = x.map { |val| Math.exp(val) }
    sum_exp = exp_values.sum
    exp_values.map { |val| val / sum_exp }
  end
  
  def matrix_multiply(a, b)
    a.map { |row| row.each_with_index.sum { |val, j| val * b[j] } }
  end
  
  def matrix_transpose(matrix)
    matrix.transpose
  end
end

class FeedForwardNetwork
  def initialize(d_model, d_ff = 2048)
    @d_model = d_model
    @d_ff = d_ff
    
    @w1 = Array.new(d_model) { Array.new(d_ff) { rand * 0.01 } }
    @w2 = Array.new(d_ff) { Array.new(d_model) { rand * 0.01 } }
  end
  
  def forward(input)
    # First linear layer + ReLU
    hidden = matrix_multiply(input, @w1)
    hidden = hidden.map { |row| row.map { |val| [0, val].max } }
    
    # Second linear layer
    matrix_multiply(hidden, @w2)
  end
  
  private
  
  def matrix_multiply(a, b)
    a.map { |row| row.each_with_index.sum { |val, j| val * b[j] } }
  end
end

class LayerNormalization
  def initialize(d_model)
    @d_model = d_model
    @gamma = Array.new(d_model) { 1.0 }
    @beta = Array.new(d_model) { 0.0 }
  end
  
  def forward(input)
    # Calculate mean and variance
    mean = input.map { |row| row.sum / @d_model }.sum / input.length
    variance = input.map { |row| row.map { |val| (val - mean) ** 2 }.sum / @d_model }.sum / input.length
    
    # Normalize
    normalized = input.map do |row|
      row.map { |val| (val - mean) / Math.sqrt(variance + 1e-10) }
    end
    
    # Scale and shift
    normalized.map do |row|
      row.each_with_index.map { |val, i| val * @gamma[i] + @beta[i] }
    end
  end
end

class LinearLayer
  def initialize(input_dim, output_dim)
    @weights = Array.new(input_dim) { Array.new(output_dim) { rand * 0.01 } }
    @bias = Array.new(output_dim) { 0.0 }
  end
  
  def forward(input)
    if input.is_a?(Array) && input.first.is_a?(Array)
      # Matrix input
      input.map { |row| matrix_vector_multiply(@weights, row) + @bias }
    else
      # Vector input
      matrix_vector_multiply(@weights, input) + @bias
    end
  end
  
  private
  
  def matrix_vector_multiply(matrix, vector)
    matrix.map { |row| row.each_with_index.sum { |val, i| val * vector[i] } }
  end
end
```

## Computer Vision

### Advanced Computer Vision Pipeline

```ruby
class ComputerVisionProcessor
  def initialize
    @models = {}
    @preprocessors = {}
    @feature_extractors = {}
  end
  
  def load_image(image_path)
    # Simplified image loading
    {
      width: 100,
      height: 100,
      channels: 3,
      data: Array.new(100 * 100 * 3) { rand(255) }
    }
  end
  
  def preprocess_image(image, target_size = [224, 224])
    # Resize image
    resized = resize_image(image, target_size)
    
    # Normalize pixel values
    normalized = normalize_pixels(resized)
    
    # Convert to tensor format
    {
      width: target_size[0],
      height: target_size[1],
      channels: image[:channels],
      data: normalized
    }
  end
  
  def extract_features(image, method: :hog)
    case method
    when :hog
      extract_hog_features(image)
    when :sift
      extract_sift_features(image)
    when :cnn
      extract_cnn_features(image)
    else
      raise ArgumentError, "Unknown feature extraction method: #{method}"
    end
  end
  
  def detect_objects(image, model: :yolo)
    case model
    when :yolo
      yolo_object_detection(image)
    when :ssd
      ssd_object_detection(image)
    when :rcnn
      rcnn_object_detection(image)
    else
      raise ArgumentError, "Unknown object detection model: #{model}"
    end
  end
  
  def classify_image(image, model: :resnet)
    case model
    when :resnet
      resnet_classification(image)
    when :vgg
      vgg_classification(image)
    when :efficientnet
      efficientnet_classification(image)
    else
      raise ArgumentError, "Unknown classification model: #{model}"
    end
  end
  
  def segment_image(image, method: :semantic)
    case method
    when :semantic
      semantic_segmentation(image)
    when :instance
      instance_segmentation(image)
    when :panoptic
      panoptic_segmentation(image)
    else
      raise ArgumentError, "Unknown segmentation method: #{method}"
    end
  end
  
  def detect_faces(image)
    # Face detection using Haar cascades (simplified)
    faces = []
    
    # Sliding window approach
    window_sizes = [20, 30, 40, 50]
    
    window_sizes.each do |window_size|
      (0..image[:width] - window_size).step(10) do |x|
        (0..image[:height] - window_size).step(10) do |y|
          window = extract_window(image, x, y, window_size)
          
          if is_face_window?(window)
            faces << {
              x: x,
              y: y,
              width: window_size,
              height: window_size,
              confidence: calculate_face_confidence(window)
            }
          end
        end
      end
    end
    
    # Non-maximum suppression
    non_maximum_suppression(faces)
  end
  
  def recognize_faces(image, known_faces)
    detected_faces = detect_faces(image)
    
    recognized_faces = detected_faces.map do |face|
      face_features = extract_face_features(image, face)
      
      best_match = known_faces.max_by do |known_face|
        calculate_face_similarity(face_features, known_face[:features])
      end
      
      if best_match && calculate_face_similarity(face_features, best_match[:features]) > 0.8
        face.merge(
          identity: best_match[:identity],
          confidence: calculate_face_similarity(face_features, best_match[:features])
        )
      else
        face.merge(identity: 'unknown')
      end
    end
    
    recognized_faces
  end
  
  def track_objects(video_frames)
    # Object tracking using Kalman filter (simplified)
    tracks = []
    
    video_frames.each_with_index do |frame, frame_idx|
      detected_objects = detect_objects(frame)
      
      if frame_idx == 0
        # Initialize tracks
        detected_objects.each do |obj|
          tracks << {
            id: tracks.length,
            bbox: obj[:bbox],
            class: obj[:class],
            confidence: obj[:confidence],
            history: [obj[:bbox]]
          }
        end
      else
        # Update existing tracks and create new ones
        updated_tracks = []
        
        tracks.each do |track|
          # Find best match
          best_match = find_best_match(track, detected_objects)
          
          if best_match
            # Update track
            track[:bbox] = best_match[:bbox]
            track[:confidence] = best_match[:confidence]
            track[:history] << best_match[:bbox]
            updated_tracks << track
            
            # Remove matched detection
            detected_objects.delete(best_match)
          else
            # Keep track if not too old
            updated_tracks << track if track[:history].length < 10
          end
        end
        
        # Create new tracks for unmatched detections
        detected_objects.each do |obj|
          updated_tracks << {
            id: updated_tracks.length,
            bbox: obj[:bbox],
            class: obj[:class],
            confidence: obj[:confidence],
            history: [obj[:bbox]]
          }
        end
        
        tracks = updated_tracks
      end
      
      yield tracks, frame_idx if block_given?
    end
    
    tracks
  end
  
  def generate_image_caption(image)
    # Image captioning using CNN + LSTM (simplified)
    image_features = extract_cnn_features(image)
    
    caption = generate_caption_from_features(image_features)
    
    caption
  end
  
  def style_transfer(content_image, style_image)
    # Neural style transfer (simplified)
    content_features = extract_cnn_features(content_image)
    style_features = extract_style_features(style_image)
    
    # Generate stylized image
    stylized_image = optimize_style_transfer(content_features, style_features)
    
    stylized_image
  end
  
  def enhance_image(image, enhancement_type: :super_resolution)
    case enhancement_type
    when :super_resolution
      super_resolution_enhancement(image)
    when :denoising
      denoising_enhancement(image)
    when :deblurring
      deblurring_enhancement(image)
    else
      raise ArgumentError, "Unknown enhancement type: #{enhancement_type}"
    end
  end
  
  private
  
  def resize_image(image, target_size)
    # Simplified image resizing
    {
      width: target_size[0],
      height: target_size[1],
      channels: image[:channels],
      data: Array.new(target_size[0] * target_size[1] * image[:channels]) { rand(255) }
    }
  end
  
  def normalize_pixels(image)
    # Normalize pixel values to [0, 1]
    image[:data].map { |pixel| pixel / 255.0 }
  end
  
  def extract_hog_features(image)
    # Histogram of Oriented Gradients (simplified)
    cell_size = 8
    block_size = 16
    bin_count = 9
    
    # Calculate gradients
    gradients = calculate_gradients(image)
    
    # Create histogram
    histogram = Array.new(bin_count) { 0 }
    
    gradients.each do |gradient|
      bin = (gradient[:orientation] / (Math::PI / bin_count)).to_i
      histogram[bin % bin_count] += gradient[:magnitude]
    end
    
    # Normalize histogram
    histogram_sum = histogram.sum
    normalized_histogram = histogram.map { |h| histogram_sum > 0 ? h / histogram_sum : 0 }
    
    normalized_histogram
  end
  
  def extract_sift_features(image)
    # Scale-Invariant Feature Transform (simplified)
    keypoints = detect_keypoints(image)
    
    features = keypoints.map do |keypoint|
      descriptor = calculate_sift_descriptor(image, keypoint)
      
      {
        x: keypoint[:x],
        y: keypoint[:y],
        scale: keypoint[:scale],
        orientation: keypoint[:orientation],
        descriptor: descriptor
      }
    end
    
    features
  end
  
  def extract_cnn_features(image)
    # CNN feature extraction (simplified)
    # In practice, this would use a pre-trained CNN model
    
    # Simulate CNN layers
    conv1 = convolution_layer(image, filters: 64, kernel_size: 3)
    pool1 = max_pooling_layer(conv1, pool_size: 2)
    
    conv2 = convolution_layer(pool1, filters: 128, kernel_size: 3)
    pool2 = max_pooling_layer(conv2, pool_size: 2)
    
    conv3 = convolution_layer(pool2, filters: 256, kernel_size: 3)
    pool3 = max_pooling_layer(conv3, pool_size: 2)
    
    # Global average pooling
    features = global_average_pooling(pool3)
    
    features
  end
  
  def yolo_object_detection(image)
    # YOLO object detection (simplified)
    grid_size = 13
    num_boxes = 5
    num_classes = 80
    
    # Simulate YOLO output
    detections = []
    
    grid_size.times do |i|
      grid_size.times do |j|
        num_boxes.times do |b|
          # Random detection for demonstration
          if rand < 0.1
            confidence = rand
            class_probs = Array.new(num_classes) { rand }
            class_id = class_probs.each_with_index.max_by { |val, i| val }[1]
            
            detections << {
              bbox: {
                x: i * (image[:width] / grid_size),
                y: j * (image[:height] / grid_size),
                width: rand * 50 + 20,
                height: rand * 50 + 20
              },
              confidence: confidence,
              class_id: class_id,
              class_name: get_class_name(class_id)
            }
          end
        end
      end
    end
    
    # Non-maximum suppression
    non_maximum_suppression(detections)
  end
  
  def semantic_segmentation(image)
    # Semantic segmentation (simplified)
    height, width = image[:height], image[:width]
    num_classes = 21
    
    # Simulate segmentation mask
    mask = Array.new(height) do
      Array.new(width) { rand(num_classes) }
    end
    
    {
      mask: mask,
      classes: (0...num_classes).to_a,
      confidence: 0.8
    }
  end
  
  def is_face_window?(window)
    # Simplified face detection logic
    # In practice, this would use a trained classifier
    
    # Check aspect ratio
    aspect_ratio = window[:width].to_f / window[:height]
    return false unless aspect_ratio.between?(0.8, 1.2)
    
    # Check size
    return false unless window[:width].between?(20, 200)
    
    # Random confidence for demonstration
    rand < 0.3
  end
  
  def calculate_face_confidence(window)
    # Simplified face confidence calculation
    rand * 0.9 + 0.1
  end
  
  def non_maximum_suppression(detections, iou_threshold = 0.5)
    return detections if detections.empty?
    
    # Sort by confidence
    sorted_detections = detections.sort_by { |d| -d[:confidence] }
    
    selected = []
    
    while sorted_detections.any?
      # Select highest confidence detection
      current = sorted_detections.shift
      selected << current
      
      # Remove overlapping detections
      sorted_detections.reject! do |det|
        calculate_iou(current[:bbox], det[:bbox]) > iou_threshold
      end
    end
    
    selected
  end
  
  def calculate_iou(bbox1, bbox2)
    # Intersection over Union
    x1 = [bbox1[:x], bbox2[:x]].max
    y1 = [bbox1[:y], bbox2[:y]].max
    x2 = [bbox1[:x] + bbox1[:width], bbox2[:x] + bbox2[:width]].min
    y2 = [bbox1[:y] + bbox1[:height], bbox2[:y] + bbox2[:height]].min
    
    intersection = [0, x2 - x1].max * [0, y2 - y1].max
    
    area1 = bbox1[:width] * bbox1[:height]
    area2 = bbox2[:width] * bbox2[:height]
    union = area1 + area2 - intersection
    
    intersection / union
  end
  
  def get_class_name(class_id)
    # Simplified class names
    classes = [
      'person', 'bicycle', 'car', 'motorcycle', 'airplane', 'bus', 'train', 'truck',
      'boat', 'traffic light', 'fire hydrant', 'stop sign', 'parking meter', 'bench',
      'bird', 'cat', 'dog', 'horse', 'sheep', 'cow', 'elephant', 'bear', 'zebra',
      'giraffe', 'backpack', 'umbrella', 'handbag', 'tie', 'suitcase', 'frisbee',
      'skis', 'snowboard', 'sports ball', 'kite', 'baseball bat', 'baseball glove',
      'skateboard', 'surfboard', 'tennis racket', 'bottle', 'wine glass', 'cup',
      'fork', 'knife', 'spoon', 'bowl', 'banana', 'apple', 'sandwich', 'orange',
      'broccoli', 'carrot', 'hot dog', 'pizza', 'donut', 'cake', 'chair', 'couch',
      'potted plant', 'bed', 'dining table', 'toilet', 'tv', 'laptop', 'mouse',
      'remote', 'keyboard', 'cell phone', 'microwave', 'oven', 'toaster', 'sink',
      'refrigerator', 'book', 'clock', 'vase', 'scissors', 'teddy bear', 'hair drier',
      'toothbrush'
    ]
    
    classes[class_id] || 'unknown'
  end
  
  def calculate_gradients(image)
    # Calculate image gradients (simplified)
    gradients = []
    
    (1...image[:height] - 1).each do |y|
      (1...image[:width] - 1).each do |x|
        # Calculate gradients using Sobel operator
        gx = get_pixel(image, x + 1, y) - get_pixel(image, x - 1, y)
        gy = get_pixel(image, x, y + 1) - get_pixel(image, x, y - 1)
        
        magnitude = Math.sqrt(gx * gx + gy * gy)
        orientation = Math.atan2(gy, gx)
        
        gradients << { magnitude: magnitude, orientation: orientation }
      end
    end
    
    gradients
  end
  
  def get_pixel(image, x, y)
    # Get pixel value (simplified)
    idx = (y * image[:width] + x) * image[:channels]
    image[:data][idx] || 0
  end
  
  def detect_keypoints(image)
    # Detect keypoints for SIFT (simplified)
    keypoints = []
    
    # Simulate keypoint detection
    100.times do
      keypoints << {
        x: rand(image[:width]),
        y: rand(image[:height]),
        scale: rand * 5 + 1,
        orientation: rand * 2 * Math::PI
      }
    end
    
    keypoints
  end
  
  def calculate_sift_descriptor(image, keypoint)
    # Calculate SIFT descriptor (simplified)
    Array.new(128) { rand }
  end
  
  def convolution_layer(image, filters:, kernel_size:)
    # Simplified convolution layer
    output_channels = filters
    output_width = image[:width] - kernel_size + 1
    output_height = image[:height] - kernel_size + 1
    
    {
      width: output_width,
      height: output_height,
      channels: output_channels,
      data: Array.new(output_width * output_height * output_channels) { rand }
    }
  end
  
  def max_pooling_layer(image, pool_size:)
    # Simplified max pooling
    output_width = image[:width] / pool_size
    output_height = image[:height] / pool_size
    
    {
      width: output_width,
      height: output_height,
      channels: image[:channels],
      data: Array.new(output_width * output_height * image[:channels]) { rand }
    }
  end
  
  def global_average_pooling(image)
    # Global average pooling
    Array.new(image[:channels]) { rand }
  end
  
  def extract_face_features(image, face_bbox)
    # Extract face features for recognition
    face_image = crop_image(image, face_bbox)
    extract_cnn_features(face_image)
  end
  
  def calculate_face_similarity(features1, features2)
    # Calculate face similarity using cosine similarity
    dot_product = features1.zip(features2).sum { |f1, f2| f1 * f2 }
    norm1 = Math.sqrt(features1.map { |f| f * f }.sum)
    norm2 = Math.sqrt(features2.map { |f| f * f }.sum)
    
    dot_product / (norm1 * norm2)
  end
  
  def crop_image(image, bbox)
    # Crop image to bounding box
    {
      width: bbox[:width],
      height: bbox[:height],
      channels: image[:channels],
      data: Array.new(bbox[:width] * bbox[:height] * image[:channels]) { rand }
    }
  end
  
  def find_best_match(track, detections)
    # Find best matching detection for track (simplified)
    return nil if detections.empty?
    
    detections.min_by do |detection|
      calculate_bbox_distance(track[:bbox], detection[:bbox])
    end
  end
  
  def calculate_bbox_distance(bbox1, bbox2)
    # Calculate distance between bounding boxes
    center1_x = bbox1[:x] + bbox1[:width] / 2
    center1_y = bbox1[:y] + bbox1[:height] / 2
    
    center2_x = bbox2[:x] + bbox2[:width] / 2
    center2_y = bbox2[:y] + bbox2[:height] / 2
    
    Math.sqrt((center1_x - center2_x) ** 2 + (center1_y - center2_y) ** 2)
  end
  
  def extract_window(image, x, y, size)
    # Extract window from image
    {
      width: size,
      height: size,
      channels: image[:channels],
      data: Array.new(size * size * image[:channels]) { rand }
    }
  end
  
  def generate_caption_from_features(features)
    # Generate caption from image features (simplified)
    templates = [
      "A photo of a {object}",
      "An image showing a {object}",
      "A picture containing a {object}",
      "The image depicts a {object}"
    ]
    
    objects = ['cat', 'dog', 'car', 'person', 'building', 'tree', 'flower']
    template = templates.sample
    object = objects.sample
    
    template.gsub('{object}', object)
  end
  
  def extract_style_features(image)
    # Extract style features for style transfer
    extract_cnn_features(image)
  end
  
  def optimize_style_transfer(content_features, style_features)
    # Optimize for style transfer (simplified)
    {
      width: 256,
      height: 256,
      channels: 3,
      data: Array.new(256 * 256 * 3) { rand }
    }
  end
  
  def super_resolution_enhancement(image)
    # Super resolution enhancement (simplified)
    {
      width: image[:width] * 2,
      height: image[:height] * 2,
      channels: image[:channels],
      data: Array.new(image[:width] * 2 * image[:height] * 2 * image[:channels]) { rand }
    }
  end
  
  def denoising_enhancement(image)
    # Denoising enhancement (simplified)
    {
      width: image[:width],
      height: image[:height],
      channels: image[:channels],
      data: image[:data].map { |pixel| pixel + rand * 10 - 5 }
    }
  end
  
  def deblurring_enhancement(image)
    # Deblurring enhancement (simplified)
    {
      width: image[:width],
      height: image[:height],
      channels: image[:channels],
      data: image[:data].map { |pixel| pixel + rand * 5 - 2.5 }
    }
  end
end
```

## Reinforcement Learning

### Advanced Reinforcement Learning Framework

```ruby
class ReinforcementLearningAgent
  def initialize(state_size, action_size, learning_rate = 0.001, gamma = 0.99)
    @state_size = state_size
    @action_size = action_size
    @learning_rate = learning_rate
    @gamma = gamma
    
    @memory = []
    @epsilon = 1.0
    @epsilon_min = 0.01
    @epsilon_decay = 0.995
    
    @q_network = NeuralNetwork.new([state_size, 64, 64, action_size], learning_rate)
    @target_network = NeuralNetwork.new([state_size, 64, 64, action_size], learning_rate)
    
    update_target_network
  end
  
  def remember(state, action, reward, next_state, done)
    @memory << {
      state: state,
      action: action,
      reward: reward,
      next_state: next_state,
      done: done
    }
    
    # Limit memory size
    @memory = @memory.last(10000)
  end
  
  def act(state)
    if rand < @epsilon
      # Explore: random action
      rand(@action_size)
    else
      # Exploit: best action
      q_values = @q_network.forward(state)
      q_values.each_with_index.max_by { |val, i| val }[1]
    end
  end
  
  def replay(batch_size = 32)
    return if @memory.length < batch_size
    
    # Sample random batch from memory
    batch = @memory.sample(batch_size)
    
    batch.each do |experience|
      state = experience[:state]
      action = experience[:action]
      reward = experience[:reward]
      next_state = experience[:next_state]
      done = experience[:done]
      
      # Calculate target Q-value
      if done
        target = reward
      else
        next_q_values = @target_network.forward(next_state)
        max_next_q = next_q_values.max
        target = reward + @gamma * max_next_q
      end
      
      # Get current Q-values
      current_q_values = @q_network.forward(state)
      
      # Update Q-value for taken action
      target_q_values = current_q_values.dup
      target_q_values[action] = target
      
      # Train network
      @q_network.backward(target_q_values)
    end
    
    # Decay epsilon
    @epsilon = [@epsilon * @epsilon_decay, @epsilon_min].max
  end
  
  def update_target_network
    # Copy weights from Q-network to target network
    @target_network.instance_variable_set(:@weights, 
      @q_network.instance_variable_get(:@weights).map(&:dup))
  end
  
  def save_model(filename)
    @q_network.save_model(filename)
  end
  
  def load_model(filename)
    @q_network = NeuralNetwork.load_model(filename)
    update_target_network
  end
end

class Environment
  def initialize
    @state = nil
    @episode = 0
    @steps = 0
    @total_reward = 0
  end
  
  def reset
    @episode += 1
    @steps = 0
    @total_reward = 0
    @state = get_initial_state
    @state
  end
  
  def step(action)
    @steps += 1
    
    # Execute action and get results
    next_state, reward, done, info = execute_action(action)
    
    @total_reward += reward
    
    [next_state, reward, done, info]
  end
  
  def render
    # Render environment (optional)
    puts "Episode #{@episode}, Step #{@steps}, Total Reward: #{@total_reward}"
  end
  
  def close
    # Clean up resources
  end
  
  private
  
  def get_initial_state
    # Return initial state
    Array.new(10) { rand }
  end
  
  def execute_action(action)
    # Execute action and return next_state, reward, done, info
    next_state = Array.new(10) { rand }
    reward = rand * 2 - 1  # Random reward between -1 and 1
    done = @steps > 100  # Episode ends after 100 steps
    info = { episode: @episode, step: @steps }
    
    [next_state, reward, done, info]
  end
end

class DQNAgent < ReinforcementLearningAgent
  def train(env, episodes = 1000)
    scores = []
    
    episodes.times do |episode|
      state = env.reset
      total_reward = 0
      done = false
      
      while !done
        action = act(state)
        next_state, reward, done, _ = env.step(action)
        
        remember(state, action, reward, next_state, done)
        state = next_state
        total_reward += reward
        
        replay(32)
      end
      
      scores << total_reward
      
      # Update target network every 10 episodes
      update_target_network if episode % 10 == 0
      
      puts "Episode #{episode + 1}, Score: #{total_reward.round(2)}, Epsilon: #{@epsilon.round(3)}"
    end
    
    scores
  end
end

class PolicyGradientAgent
  def initialize(state_size, action_size, learning_rate = 0.001)
    @state_size = state_size
    @action_size = action_size
    @learning_rate = learning_rate
    
    @policy_network = NeuralNetwork.new([state_size, 64, 64, action_size], learning_rate)
    @memory = []
  end
  
  def act(state)
    # Get action probabilities from policy network
    action_probs = @policy_network.forward(state)
    
    # Sample action from probability distribution
    sample_from_distribution(action_probs)
  end
  
  def remember(state, action, reward)
    @memory << { state: state, action: action, reward: reward }
  end
  
  def update_policy
    return if @memory.empty?
    
    # Calculate discounted rewards
    discounted_rewards = calculate_discounted_rewards
    
    # Update policy network
    @memory.each_with_index do |experience, i|
      state = experience[:state]
      action = experience[:action]
      reward = discounted_rewards[i]
      
      # Forward pass
      action_probs = @policy_network.forward(state)
      
      # Calculate loss (policy gradient)
      loss = -Math.log(action_probs[action] + 1e-10) * reward
      
      # Backward pass (simplified)
      grad = action_probs.dup
      grad[action] -= 1
      grad = grad.map { |g| g * reward }
      
      # Update network
      @policy_network.backward(grad)
    end
    
    @memory.clear
  end
  
  private
  
  def calculate_discounted_rewards(gamma = 0.99)
    rewards = @memory.map { |exp| exp[:reward] }
    discounted_rewards = Array.new(rewards.length)
    
    discounted_reward = 0
    rewards.reverse.each_with_index do |reward, i|
      discounted_reward = reward + gamma * discounted_reward
      discounted_rewards[rewards.length - 1 - i] = discounted_reward
    end
    
    # Normalize rewards
    mean = discounted_rewards.sum / discounted_rewards.length
    std = Math.sqrt(discounted_rewards.map { |r| (r - mean) ** 2 }.sum / discounted_rewards.length)
    
    discounted_rewards.map { |r| (r - mean) / (std + 1e-10) }
  end
  
  def sample_from_distribution(probabilities)
    random_val = rand
    cumulative_prob = 0.0
    
    probabilities.each_with_index do |prob, i|
      cumulative_prob += prob
      return i if random_val <= cumulative_prob
    end
    
    probabilities.length - 1
  end
end

class ActorCriticAgent
  def initialize(state_size, action_size, learning_rate = 0.001)
    @state_size = state_size
    @action_size = action_size
    @learning_rate = learning_rate
    
    @actor = NeuralNetwork.new([state_size, 64, 64, action_size], learning_rate)
    @critic = NeuralNetwork.new([state_size, 64, 64, 1], learning_rate)
    
    @memory = []
  end
  
  def act(state)
    # Get action probabilities from actor
    action_probs = @actor.forward(state)
    
    # Sample action from probability distribution
    sample_from_distribution(action_probs)
  end
  
  def remember(state, action, reward, next_state, done)
    @memory << {
      state: state,
      action: action,
      reward: reward,
      next_state: next_state,
      done: done
    }
  end
  
  def update
    return if @memory.empty?
    
    @memory.each do |experience|
      state = experience[:state]
      action = experience[:action]
      reward = experience[:reward]
      next_state = experience[:next_state]
      done = experience[:done]
      
      # Calculate advantage
      if done
        advantage = reward
      else
        next_value = @critic.forward(next_state).first
        current_value = @critic.forward(state).first
        advantage = reward + 0.99 * next_value - current_value
      end
      
      # Update actor
      action_probs = @actor.forward(state)
      actor_loss = -Math.log(action_probs[action] + 1e-10) * advantage
      
      # Update critic
      target_value = reward + (done ? 0 : 0.99 * @critic.forward(next_state).first)
      current_value = @critic.forward(state).first
      critic_loss = (target_value - current_value) ** 2
      
      # Backward pass (simplified)
      @actor.backward([actor_loss])
      @critic.backward([critic_loss])
    end
    
    @memory.clear
  end
  
  private
  
  def sample_from_distribution(probabilities)
    random_val = rand
    cumulative_prob = 0.0
    
    probabilities.each_with_index do |prob, i|
      cumulative_prob += prob
      return i if random_val <= cumulative_prob
    end
    
    probabilities.length - 1
  end
end

class MultiArmedBandit
  def initialize(num_arms)
    @num_arms = num_arms
    @q_values = Array.new(num_arms) { 0.0 }
    @action_counts = Array.new(num_arms) { 0 }
    @total_steps = 0
  end
  
  def select_action(epsilon = 0.1)
    @total_steps += 1
    
    if rand < epsilon
      # Explore: random action
      rand(@num_arms)
    else
      # Exploit: best action
      @q_values.each_with_index.max_by { |val, i| val }[1]
    end
  end
  
  def update(action, reward)
    @action_counts[action] += 1
    
    # Update Q-value using incremental average
    alpha = 1.0 / @action_counts[action]
    @q_values[action] += alpha * (reward - @q_values[action])
  end
  
  def get_q_values
    @q_values.dup
  end
  
  def get_action_counts
    @action_counts.dup
  end
end

class QLearningAgent
  def initialize(state_size, action_size, learning_rate = 0.1, gamma = 0.99, epsilon = 0.1)
    @state_size = state_size
    @action_size = action_size
    @learning_rate = learning_rate
    @gamma = gamma
    @epsilon = epsilon
    
    @q_table = Hash.new { |h, k| h[k] = Array.new(action_size) { 0.0 } }
  end
  
  def get_action(state)
    if rand < @epsilon
      rand(@action_size)
    else
      q_values = @q_table[state]
      q_values.each_with_index.max_by { |val, i| val }[1]
    end
  end
  
  def update(state, action, reward, next_state)
    current_q = @q_table[state][action]
    max_next_q = @q_table[next_state].max
    
    # Q-learning update rule
    new_q = current_q + @learning_rate * (reward + @gamma * max_next_q - current_q)
    @q_table[state][action] = new_q
  end
  
  def get_q_table
    @q_table.dup
  end
end

class MonteCarloAgent
  def initialize(state_size, action_size, gamma = 0.99)
    @state_size = state_size
    @action_size = action_size
    @gamma = gamma
    
    @returns = Hash.new { |h, k| h[k] = [] }
    @q_values = Hash.new { |h, k| h[k] = Array.new(action_size) { 0.0 } }
    @policy = Hash.new { |h, k| h[k] = rand(action_size) }
  end
  
  def get_action(state)
    @policy[state]
  end
  
  def update_episode(episode)
    # Calculate returns for each state-action pair
    G = 0
    
    episode.reverse_each do |state, action, reward|
      G = reward + @gamma * G
      
      # Append return to state-action pair
      state_action = "#{state}-#{action}"
      @returns[state_action] << G
      
      # Update Q-value
      @q_values[state][action] = @returns[state_action].sum / @returns[state_action].length
      
      # Update policy (greedy)
      best_action = @q_values[state].each_with_index.max_by { |val, i| val }[1]
      @policy[state] = best_action
    end
  end
end

class TrainingManager
  def initialize
    @agents = {}
    @environments = {}
    @results = {}
  end
  
  def register_agent(name, agent)
    @agents[name] = agent
  end
  
  def register_environment(name, environment)
    @environments[name] = environment
  end
  
  def train_agent(agent_name, env_name, config = {})
    agent = @agents[agent_name]
    env = @environments[env_name]
    
    raise ArgumentError, "Unknown agent: #{agent_name}" unless agent
    raise ArgumentError, "Unknown environment: #{env_name}" unless env
    
    episodes = config[:episodes] || 1000
    max_steps = config[:max_steps] || 1000
    
    results = []
    
    episodes.times do |episode|
      state = env.reset
      total_reward = 0
      episode_data = []
      
      max_steps.times do |step|
        action = agent.act(state)
        next_state, reward, done, info = env.step(action)
        
        episode_data << [state, action, reward, next_state, done]
        
        if agent.respond_to?(:remember)
          agent.remember(state, action, reward, next_state, done)
        end
        
        if agent.respond_to?(:update)
          agent.update
        elsif agent.respond_to?(:replay)
          agent.replay
        end
        
        total_reward += reward
        state = next_state
        
        break if done
      end
      
      if agent.respond_to?(:update_episode)
        agent.update_episode(episode_data)
      end
      
      results << total_reward
      
      if (episode + 1) % 100 == 0
        avg_reward = results[-100..-1].sum / 100
        puts "Episode #{episode + 1}, Average Reward (last 100): #{avg_reward.round(2)}"
      end
    end
    
    @results["#{agent_name}_#{env_name}"] = results
    results
  end
  
  def compare_agents(agent_names, env_name, config = {})
    comparison_results = {}
    
    agent_names.each do |agent_name|
      puts "Training #{agent_name}..."
      results = train_agent(agent_name, env_name, config)
      comparison_results[agent_name] = results
    end
    
    # Generate comparison report
    generate_comparison_report(comparison_results)
  end
  
  def hyperparameter_tuning(agent_name, env_name, param_grid)
    best_score = -Float::INFINITY
    best_params = nil
    tuning_results = []
    
    # Generate all parameter combinations
    param_combinations = generate_param_combinations(param_grid)
    
    param_combinations.each_with_index do |params, i|
      puts "Testing parameter combination #{i + 1}/#{param_combinations.length}: #{params}"
      
      # Create agent with current parameters
      agent = create_agent_with_params(agent_name, params)
      
      # Train agent
      results = train_agent_custom(agent, env_name, params[:episodes] || 500)
      
      # Evaluate performance
      avg_score = results[-100..-1].sum / 100
      
      tuning_results << {
        params: params,
        avg_score: avg_score,
        results: results
      }
      
      # Update best parameters
      if avg_score > best_score
        best_score = avg_score
        best_params = params
      end
    end
    
    {
      best_params: best_params,
      best_score: best_score,
      tuning_results: tuning_results
    }
  end
  
  private
  
  def generate_comparison_report(results)
    puts "\n=== Agent Comparison Report ==="
    
    results.each do |agent_name, scores|
      avg_score = scores.sum / scores.length
      std_score = Math.sqrt(scores.map { |s| (s - avg_score) ** 2 }.sum / scores.length)
      
      puts "#{agent_name}:"
      puts "  Average Score: #{avg_score.round(2)}"
      puts "  Standard Deviation: #{std_score.round(2)}"
      puts "  Max Score: #{scores.max.round(2)}"
      puts "  Min Score: #{scores.min.round(2)}"
      puts
    end
  end
  
  def generate_param_combinations(param_grid)
    combinations = [{}]
    
    param_grid.each do |param, values|
      new_combinations = []
      
      combinations.each do |combination|
        values.each do |value|
          new_combination = combination.dup
          new_combination[param] = value
          new_combinations << new_combination
        end
      end
      
      combinations = new_combinations
    end
    
    combinations
  end
  
  def create_agent_with_params(agent_type, params)
    case agent_type
    when :dqn
      ReinforcementLearningAgent.new(
        params[:state_size] || 10,
        params[:action_size] || 4,
        params[:learning_rate] || 0.001,
        params[:gamma] || 0.99
      )
    when :policy_gradient
      PolicyGradientAgent.new(
        params[:state_size] || 10,
        params[:action_size] || 4,
        params[:learning_rate] || 0.001
      )
    when :actor_critic
      ActorCriticAgent.new(
        params[:state_size] || 10,
        params[:action_size] || 4,
        params[:learning_rate] || 0.001
      )
    else
      raise ArgumentError, "Unknown agent type: #{agent_type}"
    end
  end
  
  def train_agent_custom(agent, env_name, episodes)
    env = @environments[env_name]
    results = []
    
    episodes.times do
      state = env.reset
      total_reward = 0
      done = false
      
      while !done
        action = agent.act(state)
        next_state, reward, done, _ = env.step(action)
        
        if agent.respond_to?(:remember)
          agent.remember(state, action, reward, next_state, done)
        end
        
        if agent.respond_to?(:update)
          agent.update
        elsif agent.respond_to?(:replay)
          agent.replay
        end
        
        total_reward += reward
        state = next_state
      end
      
      results << total_reward
    end
    
    results
  end
end
```

## Practice Exercises

### Exercise 1: Complete AI Framework
Build a comprehensive AI framework with:
- Multiple neural network architectures
- Advanced optimization algorithms
- Distributed training support
- Model serving and deployment

### Exercise 2: Computer Vision Application
Create a complete computer vision application:
- Real-time object detection
- Face recognition system
- Image enhancement pipeline
- Video analysis tools

### Exercise 3: NLP Platform
Build an advanced NLP platform:
- Multi-language support
- Custom model training
- Real-time translation
- Sentiment analysis dashboard

### Exercise 4: Reinforcement Learning Environment
Create a complex RL environment:
- 3D simulation environment
- Multi-agent scenarios
- Custom reward functions
- Performance visualization

---

**Ready to push the boundaries of artificial intelligence? Let's dive into advanced AI development in Ruby! 🤖**
