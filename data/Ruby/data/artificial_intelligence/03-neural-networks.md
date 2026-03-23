# Neural Networks in Ruby
# Comprehensive guide to neural network implementation and training

## 🧠 Neural Network Fundamentals

### 1. Neural Network Architecture

Core concepts and components:

```ruby
class NeuralNetworkBasics
  def self.explain_neural_concepts
    puts "Neural Network Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Neuron",
        description: "Basic computational unit",
        components: ["Inputs", "Weights", "Bias", "Activation function"],
        operation: "Output = activation(Σ(inputs × weights) + bias)"
      },
      {
        concept: "Layer",
        description: "Collection of neurons",
        types: ["Input layer", "Hidden layers", "Output layer"],
        purpose: "Transform data through network"
      },
      {
        concept: "Activation Function",
        description: "Introduces non-linearity",
        examples: ["Sigmoid", "ReLU", "Tanh", "Softmax"],
        importance: "Enables learning complex patterns"
      },
      {
        concept: "Forward Propagation",
        description: "Data flow through network",
        process: "Input → Hidden → Output",
        purpose: "Generate predictions"
      },
      {
        concept: "Backpropagation",
        description: "Error backward flow",
        process: "Calculate gradients, update weights",
        purpose: "Train the network"
      },
      {
        concept: "Loss Function",
        description: "Measure prediction error",
        examples: ["MSE", "Cross-entropy", "Huber loss"],
        purpose: "Guide weight updates"
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Examples: #{concept[:examples].join(', ')}" if concept[:examples]
      puts "  Operation: #{concept[:operation]}" if concept[:operation]
      puts "  Purpose: #{concept[:purpose]}" if concept[:purpose]
      puts "  Process: #{concept[:process]}" if concept[:process]
      puts
    end
  end
  
  def self.activation_functions
    puts "\nActivation Functions:"
    puts "=" * 50
    
    functions = [
      {
        name: "Sigmoid",
        formula: "σ(x) = 1 / (1 + e^(-x))",
        range: "(0, 1)",
        derivative: "σ'(x) = σ(x) × (1 - σ(x))",
        use_case: "Binary classification output"
      },
      {
        name: "Tanh",
        formula: "tanh(x) = (e^x - e^(-x)) / (e^x + e^(-x))",
        range: "(-1, 1)",
        derivative: "tanh'(x) = 1 - tanh²(x)",
        use_case: "Hidden layer activation"
      },
      {
        name: "ReLU",
        formula: "ReLU(x) = max(0, x)",
        range: "[0, ∞)",
        derivative: "ReLU'(x) = 1 if x > 0, 0 otherwise",
        use_case: "Hidden layer activation (most common)"
      },
      {
        name: "Leaky ReLU",
        formula: "LReLU(x) = max(αx, x)",
        range: "(-∞, ∞)",
        derivative: "LReLU'(x) = 1 if x > 0, α otherwise",
        use_case: "Hidden layer (solves dying ReLU problem)"
      },
      {
        name: "Softmax",
        formula: "softmax(x_i) = e^x_i / Σ(e^x_j)",
        range: "(0, 1)",
        derivative: "Complex (uses cross-entropy)",
        use_case: "Multi-class classification output"
      }
    ]
    
    functions.each do |func|
      puts "#{func[:name]}:"
      puts "  Formula: #{func[:formula]}"
      puts "  Range: #{func[:range]}"
      puts "  Derivative: #{func[:derivative]}"
      puts "  Use case: #{func[:use_case]}"
      puts
    end
  end
  
  # Run neural network basics
  explain_neural_concepts
  activation_functions
end
```

### 2. Activation Functions

Implementation of common activation functions:

```ruby
class ActivationFunctions
  def self.sigmoid(x)
    1.0 / (1.0 + Math.exp(-x))
  end
  
  def self.sigmoid_derivative(x)
    s = sigmoid(x)
    s * (1 - s)
  end
  
  def self.tanh(x)
    Math.tanh(x)
  end
  
  def self.tanh_derivative(x)
    1 - tanh(x) ** 2
  end
  
  def self.relu(x)
    [0, x].max
  end
  
  def self.relu_derivative(x)
    x > 0 ? 1 : 0
  end
  
  def self.leaky_relu(x, alpha = 0.01)
    x > 0 ? x : alpha * x
  end
  
  def self.leaky_relu_derivative(x, alpha = 0.01)
    x > 0 ? 1 : alpha
  end
  
  def self.softmax(x)
    exp_x = x.map { |val| Math.exp(val) }
    sum_exp = exp_x.sum
    exp_x.map { |val| val / sum_exp }
  end
  
  def self.demonstrate_activations
    puts "Activation Functions Demonstration:"
    puts "=" * 50
    
    test_values = [-2, -1, -0.5, 0, 0.5, 1, 2]
    
    puts "Test values: #{test_values}"
    
    puts "\nSigmoid:"
    test_values.each do |x|
      result = sigmoid(x)
      puts "  sigmoid(#{x}) = #{result.round(4)}"
    end
    
    puts "\nTanh:"
    test_values.each do |x|
      result = tanh(x)
      puts "  tanh(#{x}) = #{result.round(4)}"
    end
    
    puts "\nReLU:"
    test_values.each do |x|
      result = relu(x)
      puts "  ReLU(#{x}) = #{result.round(4)}"
    end
    
    puts "\nLeaky ReLU (α=0.01):"
    test_values.each do |x|
      result = leaky_relu(x)
      puts "  LeakyReLU(#{x}) = #{result.round(4)}"
    end
    
    puts "\nSoftmax:"
    test_vector = [1.0, 2.0, 3.0, 4.0]
    result = softmax(test_vector)
    puts "  Input: #{test_vector}"
    puts "  Output: #{result.map { |v| v.round(4) }}"
    puts "  Sum: #{result.sum.round(4)}"
  end
end
```

## 🏗️ Neural Network Implementation

### 3. Simple Neural Network

Basic feedforward neural network:

```ruby
class SimpleNeuralNetwork
  def initialize(layer_sizes, learning_rate = 0.01)
    @layer_sizes = layer_sizes
    @learning_rate = learning_rate
    @weights = []
    @biases = []
    @activations = []
    @z_values = []
    
    # Initialize weights and biases
    (layer_sizes.length - 1).times do |i|
      input_size = layer_sizes[i]
      output_size = layer_sizes[i + 1]
      
      # Xavier initialization
      limit = Math.sqrt(6.0 / (input_size + output_size))
      @weights << Array.new(output_size) do
        Array.new(input_size) { rand * 2 * limit - limit }
      end
      
      @biases << Array.new(output_size, 0)
      @activations << Array.new(output_size, 0)
      @z_values << Array.new(output_size, 0)
    end
  end
  
  def forward(input)
    current_input = input
    
    @layer_sizes.each_with_index do |size, layer_idx|
      if layer_idx == 0
        # Input layer
        @activations[layer_idx] = current_input
      else
        # Hidden or output layer
        weights_idx = layer_idx - 1
        
        # Calculate weighted sum
        @z_values[weights_idx] = @weights[weights_idx].map.with_index do |weights_row, j|
          bias = @biases[weights_idx][j]
          weighted_sum = current_input.zip(weights_row).sum { |x, w| x * w }
          weighted_sum + bias
        end
        
        # Apply activation
        if layer_idx == @layer_sizes.length - 1
          # Output layer - use sigmoid for binary classification
          @activations[layer_idx] = @z_values[weights_idx].map { |z| ActivationFunctions.sigmoid(z) }
        else
          # Hidden layers - use ReLU
          @activations[layer_idx] = @z_values[weights_idx].map { |z| ActivationFunctions.relu(z) }
        end
        
        current_input = @activations[layer_idx]
      end
    end
    
    @activations.last
  end
  
  def backward(input, target)
    # Forward pass
    output = forward(input)
    
    # Calculate output layer error
    output_error = @activations.last.zip(target).map do |pred, actual|
      pred - actual
    end
    
    # Calculate output layer delta
    output_delta = output_error.zip(@z_values.last).map do |error, z|
      error * ActivationFunctions.sigmoid_derivative(z)
    end
    
    # Backpropagate through layers
    deltas = [output_delta]
    
    (@layer_sizes.length - 2).downto(1) do |layer_idx|
      weights_idx = layer_idx
      next_weights = @weights[weights_idx + 1]
      
      # Calculate error for this layer
      layer_error = Array.new(@layer_sizes[layer_idx], 0)
      
      output_delta.each_with_index do |next_delta, i|
        next_weights[i].each_with_index do |weight, j|
          layer_error[j] += next_delta * weight
        end
      end
      
      # Calculate delta for this layer
      layer_delta = layer_error.zip(@z_values[weights_idx]).map do |error, z|
        error * ActivationFunctions.relu_derivative(z)
      end
      
      deltas.unshift(layer_delta)
    end
    
    # Update weights and biases
    current_activation = input
    
    @weights.each_with_index do |layer_weights, layer_idx|
      layer_biases = @biases[layer_idx]
      layer_delta = deltas[layer_idx]
      
      # Update weights
      layer_weights.each_with_index do |weights_row, i|
        weights_row.each_with_index do |weight, j|
          gradient = layer_delta[i] * current_activation[j]
          layer_weights[i][j] = weight - @learning_rate * gradient
        end
      end
      
      # Update biases
      layer_biases.each_with_index do |bias, i|
        gradient = layer_delta[i]
        layer_biases[i] = bias - @learning_rate * gradient
      end
      
      current_activation = @activations[layer_idx + 1]
    end
  end
  
  def train(X, y, epochs = 1000, batch_size = 32)
    n_samples = X.length
    
    epochs.times do |epoch|
      total_loss = 0
      
      # Mini-batch training
      (0...n_samples).step(batch_size) do |start_idx|
        end_idx = [start_idx + batch_size, n_samples].min
        batch_x = X[start_idx...end_idx]
        batch_y = y[start_idx...end_idx]
        
        batch_x.each_with_index do |input, i|
          target = batch_y[i]
          
          # Forward and backward pass
          output = forward(input)
          backward(input, target)
          
          # Calculate loss (binary cross-entropy)
          loss = -target * Math.log(output[0] + 1e-15) - (1 - target) * Math.log(1 - output[0] + 1e-15)
          total_loss += loss
        end
      end
      
      # Print progress
      if epoch % 100 == 0
        avg_loss = total_loss / n_samples
        accuracy = calculate_accuracy(X, y)
        puts "Epoch #{epoch}: Loss = #{avg_loss.round(4)}, Accuracy = #{accuracy.round(4)}"
      end
    end
  end
  
  def predict(X)
    X.map { |input| forward(input)[0] >= 0.5 ? 1 : 0 }
  end
  
  def predict_probability(X)
    X.map { |input| forward(input)[0] }
  end
  
  def calculate_accuracy(X, y)
    predictions = predict(X)
    correct = predictions.zip(y).count { |pred, actual| pred == actual }
    correct.to_f / predictions.length
  end
  
  def self.demonstrate_simple_nn
    puts "Simple Neural Network Demonstration:"
    puts "=" * 50
    
    # Generate sample data (XOR problem)
    X = [[0, 0], [0, 1], [1, 0], [1, 1]]
    y = [0, 1, 1, 0]
    
    puts "XOR Problem:"
    X.each_with_index do |input, i|
      puts "Input: #{input} -> Output: #{y[i]}"
    end
    
    # Create neural network
    # Input: 2 neurons, Hidden: 4 neurons, Output: 1 neuron
    nn = SimpleNeuralNetwork.new([2, 4, 1], 0.1)
    
    puts "\nTraining neural network..."
    nn.train(X, y, 5000, 4)
    
    # Test network
    puts "\nTesting neural network:"
    X.each_with_index do |input, i|
      prediction = nn.predict([input])[0]
      probability = nn.predict_probability([input])[0]
      puts "Input: #{input} -> Predicted: #{prediction} (prob: #{probability.round(4)})"
    end
    
    accuracy = nn.calculate_accuracy(X, y)
    puts "\nFinal accuracy: #{accuracy.round(4)}"
  end
end
```

### 4. Multi-Layer Perceptron

More advanced neural network:

```ruby
class MultiLayerPerceptron
  def initialize(layer_sizes, learning_rate = 0.01, activation = :relu)
    @layer_sizes = layer_sizes
    @learning_rate = learning_rate
    @activation = activation
    @weights = []
    @biases = []
    
    # Initialize weights and biases
    (layer_sizes.length - 1).times do |i|
      input_size = layer_sizes[i]
      output_size = layer_sizes[i + 1]
      
      # He initialization for ReLU
      if activation == :relu
        std_dev = Math.sqrt(2.0 / input_size)
      else
        std_dev = Math.sqrt(1.0 / input_size)
      end
      
      @weights << Array.new(output_size) do
        Array.new(input_size) { rand * std_dev * 2 - std_dev }
      end
      
      @biases << Array.new(output_size, 0)
    end
  end
  
  def activate(x, activation_type = nil)
    act_type = activation_type || @activation
    
    case act_type
    when :sigmoid
      ActivationFunctions.sigmoid(x)
    when :tanh
      ActivationFunctions.tanh(x)
    when :relu
      ActivationFunctions.relu(x)
    when :leaky_relu
      ActivationFunctions.leaky_relu(x)
    else
      x
    end
  end
  
  def activate_derivative(x, activation_type = nil)
    act_type = activation_type || @activation
    
    case act_type
    when :sigmoid
      ActivationFunctions.sigmoid_derivative(x)
    when :tanh
      ActivationFunctions.tanh_derivative(x)
    when :relu
      ActivationFunctions.relu_derivative(x)
    when :leaky_relu
      ActivationFunctions.leaky_relu_derivative(x)
    else
      1
    end
  end
  
  def forward(input)
    @layer_activations = [input]
    @layer_z_values = []
    
    current_input = input
    
    @weights.each_with_index do |weights, layer_idx|
      # Calculate weighted sum
      z = weights.map.with_index do |weights_row, i|
        bias = @biases[layer_idx][i]
        weighted_sum = current_input.zip(weights_row).sum { |x, w| x * w }
        weighted_sum + bias
      end
      
      @layer_z_values << z
      
      # Apply activation
      if layer_idx == @weights.length - 1
        # Output layer - use sigmoid for binary classification
        activated = z.map { |val| activate(val, :sigmoid) }
      else
        # Hidden layers
        activated = z.map { |val| activate(val) }
      end
      
      @layer_activations << activated
      current_input = activated
    end
    
    current_input
  end
  
  def compute_loss(y_true, y_pred)
    # Binary cross-entropy loss
    y_true.zip(y_pred).sum do |true_val, pred_val|
      -true_val * Math.log(pred_val + 1e-15) - (1 - true_val) * Math.log(1 - pred_val + 1e-15)
    end / y_true.length
  end
  
  def backward(input, target)
    # Forward pass
    output = forward(input)
    
    # Calculate output layer gradient
    output_error = output.zip(target).map { |pred, actual| pred - actual }
    output_delta = output_error.zip(@layer_z_values.last).map do |error, z|
      error * activate_derivative(z, :sigmoid)
    end
    
    # Backpropagate
    deltas = [output_delta]
    
    (@layer_z_values.length - 2).downto(0).reverse_each do |layer_idx|
      weights = @weights[layer_idx + 1]
      next_delta = deltas.first
      
      # Calculate error for current layer
      layer_error = Array.new(@layer_sizes[layer_idx + 1], 0)
      
      next_delta.each_with_index do |next_d, i|
        weights[i].each_with_index do |weight, j|
          layer_error[j] += next_d * weight
        end
      end
      
      # Calculate delta for current layer
      layer_z = @layer_z_values[layer_idx]
      layer_delta = layer_error.zip(layer_z).map do |error, z|
        error * activate_derivative(z)
      end
      
      deltas.unshift(layer_delta)
    end
    
    # Update weights and biases
    @weights.each_with_index do |weights, layer_idx|
      layer_input = @layer_activations[layer_idx]
      layer_delta = deltas[layer_idx]
      
      # Update weights
      weights.each_with_index do |weights_row, i|
        weights_row.each_with_index do |weight, j|
          gradient = layer_delta[i] * layer_input[j]
          weights[i][j] = weight - @learning_rate * gradient
        end
      end
      
      # Update biases
      @biases[layer_idx].each_with_index do |bias, i|
        gradient = layer_delta[i]
        @biases[layer_idx][i] = bias - @learning_rate * gradient
      end
    end
  end
  
  def train(X, y, epochs = 1000, batch_size = 32, verbose = true)
    n_samples = X.length
    
    epochs.times do |epoch|
      total_loss = 0
      
      # Shuffle data
      indices = (0...n_samples).to_a.shuffle
      
      # Mini-batch training
      (0...n_samples).step(batch_size) do |start_idx|
        end_idx = [start_idx + batch_size, n_samples].min
        batch_indices = indices[start_idx...end_idx]
        
        batch_indices.each do |idx|
          input = X[idx]
          target = y[idx]
          
          # Forward and backward pass
          output = forward(input)
          backward(input, target)
          
          # Calculate loss
          loss = compute_loss([target], output)
          total_loss += loss
        end
      end
      
      # Print progress
      if verbose && epoch % 100 == 0
        avg_loss = total_loss / n_samples
        accuracy = calculate_accuracy(X, y)
        puts "Epoch #{epoch}: Loss = #{avg_loss.round(4)}, Accuracy = #{accuracy.round(4)}"
      end
    end
  end
  
  def predict(X)
    X.map { |input| forward(input)[0] >= 0.5 ? 1 : 0 }
  end
  
  def predict_probability(X)
    X.map { |input| forward(input)[0] }
  end
  
  def calculate_accuracy(X, y)
    predictions = predict(X)
    correct = predictions.zip(y).count { |pred, actual| pred == actual }
    correct.to_f / predictions.length
  end
  
  def self.demonstrate_mlp
    puts "Multi-Layer Perceptron Demonstration:"
    puts "=" * 50
    
    # Generate sample data (more complex pattern)
    X = []
    y = []
    
    # Generate data for a more complex pattern
    100.times do
      x1 = rand * 10
      x2 = rand * 10
      x3 = rand * 10
      
      # Complex decision boundary
      label = (x1 + x2 * 0.5 + x3 * 0.3 > 7) ? 1 : 0
      
      X << [x1, x2, x3]
      y << label
    end
    
    puts "Generated #{X.length} samples with 3 features"
    puts "Positive samples: #{y.count(1)}"
    puts "Negative samples: #{y.count(0)}"
    
    # Split data
    split_index = (X.length * 0.8).to_i
    X_train, X_test = X[0...split_index], X[split_index..-1]
    y_train, y_test = y[0...split_index], y[split_index..-1]
    
    puts "\nTraining set: #{X_train.length} samples"
    puts "Test set: #{X_test.length} samples"
    
    # Create MLP
    # Input: 3 features, Hidden: 8 neurons, Hidden: 4 neurons, Output: 1 neuron
    mlp = MultiLayerPerceptron.new([3, 8, 4, 1], 0.01, :relu)
    
    puts "\nTraining MLP..."
    mlp.train(X_train, y_train, 1000, 16)
    
    # Evaluate
    train_accuracy = mlp.calculate_accuracy(X_train, y_train)
    test_accuracy = mlp.calculate_accuracy(X_test, y_test)
    
    puts "\nModel Performance:"
    puts "Training accuracy: #{train_accuracy.round(4)}"
    puts "Test accuracy: #{test_accuracy.round(4)}"
    
    # Test predictions
    puts "\nSample predictions:"
    5.times do |i|
      input = X_test[i]
      prediction = mlp.predict([input])[0]
      probability = mlp.predict_probability([input])[0]
      actual = y_test[i]
      
      puts "Input: #{input.map { |v| v.round(2) }} -> Predicted: #{prediction} (prob: #{probability.round(4)}), Actual: #{actual}"
    end
  end
end
```

## 🧠 Advanced Neural Networks

### 5. Convolutional Neural Network

CNN implementation for image processing:

```ruby
class ConvolutionalLayer
  def initialize(input_channels, output_channels, kernel_size, stride = 1, padding = 0)
    @input_channels = input_channels
    @output_channels = output_channels
    @kernel_size = kernel_size
    @stride = stride
    @padding = padding
    
    # Initialize kernels
    @kernels = Array.new(output_channels) do
      Array.new(input_channels) do
        Array.new(kernel_size) { Array.new(kernel_size) { rand * 0.1 - 0.05 } }
      end
    end
    
    @biases = Array.new(output_channels, 0)
  end
  
  def forward(input)
    @input = input
    input_height, input_width = input[0].length, input[0][0].length
    
    # Calculate output dimensions
    output_height = ((input_height + 2 * @padding - @kernel_size) / @stride + 1).to_i
    output_width = ((input_width + 2 * @padding - @kernel_size) / @stride + 1).to_i
    
    # Initialize output
    @output = Array.new(@output_channels) do
      Array.new(output_height) { Array.new(output_width, 0) }
    end
    
    # Perform convolution
    @output_channels.times do |c|
      output_height.times do |h|
        output_width.times do |w|
          sum = 0
          
          @input_channels.times do |ic|
            @kernel_size.times do |kh|
              @kernel_size.times do |kw|
                input_h = h * @stride + kh - @padding
                input_w = w * @stride + kw - @padding
                
                if input_h >= 0 && input_h < input_height && input_w >= 0 && input_w < input_width
                  sum += input[ic][input_h][input_w] * @kernels[c][ic][kh][kw]
                end
              end
            end
          end
          
          @output[c][h][w] = ActivationFunctions.relu(sum + @biases[c])
        end
      end
    end
    
    @output
  end
  
  def backward(output_gradient, learning_rate)
    # Simplified backward pass
    input_gradient = Array.new(@input_channels) do
      Array.new(@input[0].length) { Array.new(@input[0][0].length, 0) }
    end
    
    # Update kernels and biases
    @output_channels.times do |c|
      @output_channels.times do |ic|
        @kernel_size.times do |kh|
          @kernel_size.times do |kw|
            gradient = 0
            
            @output[0].length.times do |h|
              @output[0][0].length.times do |w|
                input_h = h * @stride + kh - @padding
                input_w = w * @stride + kw - @padding
                
                if input_h >= 0 && input_h < @input[0].length && input_w >= 0 && input_w < @input[0][0].length
                  gradient += output_gradient[c][h][w] * @input[ic][input_h][input_w]
                end
              end
            end
            
            @kernels[c][ic][kh][kw] -= learning_rate * gradient
          end
        end
      end
      
      # Update bias
      bias_gradient = output_gradient[c].sum
      @biases[c] -= learning_rate * bias_gradient
    end
    
    input_gradient
  end
end

class MaxPoolingLayer
  def initialize(pool_size = 2, stride = 2)
    @pool_size = pool_size
    @stride = stride
  end
  
  def forward(input)
    @input = input
    input_channels, input_height, input_width = input.length, input[0].length, input[0][0].length
    
    # Calculate output dimensions
    output_height = (input_height / @pool_size).to_i
    output_width = (input_width / @pool_size).to_i
    
    # Initialize output
    @output = Array.new(input_channels) do
      Array.new(output_height) { Array.new(output_width, 0) }
    end
    
    @max_indices = Array.new(input_channels) do
      Array.new(output_height) { Array.new(output_width, [0, 0]) }
    end
    
    # Perform max pooling
    input_channels.times do |c|
      output_height.times do |h|
        output_width.times do |w|
          max_val = -Float::INFINITY
          max_h, max_w = 0, 0
          
          @pool_size.times do |kh|
            @pool_size.times do |kw|
              input_h = h * @pool_size + kh
              input_w = w * @pool_size + kw
              
              if input[c][input_h][input_w] > max_val
                max_val = input[c][input_h][input_w]
                max_h, max_w = input_h, input_w
              end
            end
          end
          
          @output[c][h][w] = max_val
          @max_indices[c][h][w] = [max_h, max_w]
        end
      end
    end
    
    @output
  end
  
  def backward(output_gradient, learning_rate = nil)
    input_channels, input_height, input_width = @input.length, @input[0].length, @input[0][0].length
    
    input_gradient = Array.new(input_channels) do
      Array.new(input_height) { Array.new(input_width, 0) }
    end
    
    output_height, output_width = @output[0].length, @output[0][0].length
    
    input_channels.times do |c|
      output_height.times do |h|
        output_width.times do |w|
          max_h, max_w = @max_indices[c][h][w]
          input_gradient[c][max_h][max_w] += output_gradient[c][h][w]
        end
      end
    end
    
    input_gradient
  end
end

class FlattenLayer
  def forward(input)
    @input_shape = [input.length, input[0].length, input[0][0].length]
    @output = input.flatten
  end
  
  def backward(output_gradient, learning_rate = nil)
    # Reshape gradient back to original shape
    channels, height, width = @input_shape
    output_gradient.each_slice(channels * height * width).map do |slice|
      slice.each_slice(height * width).to_a
    end
  end
end

class SimpleCNN
  def initialize(input_shape, num_classes)
    @input_shape = input_shape
    @num_classes = num_classes
    
    # Define layers
    @conv1 = ConvolutionalLayer.new(input_shape[0], 16, 3, 1, 1)
    @pool1 = MaxPoolingLayer.new(2, 2)
    
    conv_output_size = ((input_shape[1] + 2) / 2).to_i
    @flatten_size = 16 * conv_output_size * conv_output_size
    
    # Dense layers (simplified)
    @dense_weights = Array.new(num_classes) { Array.new(@flatten_size) { rand * 0.1 - 0.05 } }
    @dense_biases = Array.new(num_classes, 0)
  end
  
  def forward(input)
    # Convolution + ReLU
    conv_output = @conv1.forward(input)
    
    # Max pooling
    pool_output = @pool1.forward(conv_output)
    
    # Flatten
    flatten_input = pool_output.flatten
    
    # Dense layer
    @logits = @dense_weights.map do |weights|
      bias = @dense_biases[@dense_weights.index(weights)]
      flatten_input.zip(weights).sum { |x, w| x * w } + bias
    end
    
    # Softmax
    @output = ActivationFunctions.softmax(@logits)
  end
  
  def train_step(input, target, learning_rate = 0.01)
    # Forward pass
    output = forward(input)
    
    # Calculate loss gradient (simplified)
    output_gradient = output.dup
    output_gradient[target] -= 1
    
    # Backward through dense layer
    flatten_input = @pool1.forward(@conv1.forward(input)).flatten
    dense_gradient = flatten_input
    
    @dense_weights.each_with_index do |weights, i|
      weights.each_with_index do |weight, j|
        weights[j] -= learning_rate * output_gradient[i] * flatten_input[j]
      end
      @dense_biases[i] -= learning_rate * output_gradient[i]
    end
    
    # Backward through flatten layer
    pool_gradient = dense_gradient.each_slice(@flatten_size).map do |slice|
      slice.each_slice(16).to_a
    end
    
    # Backward through pooling layer
    conv_gradient = @pool1.backward(pool_gradient)
    
    # Backward through convolution layer
    @conv1.backward(conv_gradient, learning_rate)
    
    # Calculate loss
    loss = -Math.log(output[target] + 1e-15)
    loss
  end
  
  def predict(input)
    output = forward(input)
    output.index(output.max)
  end
  
  def self.demonstrate_cnn
    puts "Convolutional Neural Network Demonstration:"
    puts "=" * 50
    
    # Create simple CNN for MNIST-like data
    # Input: 1 channel, 28x28 image
    # Output: 10 classes
    cnn = SimpleCNN.new([1, 28, 28], 10)
    
    # Generate sample data (simplified)
    puts "Generating sample image data..."
    
    # Create sample images (28x28 grayscale)
    X = 100.times.map do
      # Generate random image with some pattern
      image = Array.new(1) do
        Array.new(28) do
          Array.new(28) { rand }
        end
      end
      
      # Add some pattern (simplified)
      center = 14
      5.times do |i|
        5.times do |j|
          image[0][center + i - 2][center + j - 2] = rand > 0.5 ? 1.0 : 0.0
        end
      end
      
      image
    end
    
    # Generate labels (0-9)
    y = 100.times.map { rand(10) }
    
    puts "Generated #{X.length} sample images"
    puts "Image shape: #{X[0][0].length}x#{X[0][0][0].length}"
    
    # Train for a few epochs
    puts "\nTraining CNN..."
    
    10.times do |epoch|
      total_loss = 0
      
      X.each_with_index do |image, i|
        loss = cnn.train_step(image, y[i], 0.001)
        total_loss += loss
      end
      
      avg_loss = total_loss / X.length
      puts "Epoch #{epoch + 1}: Loss = #{avg_loss.round(4)}"
    end
    
    # Test predictions
    puts "\nSample predictions:"
    5.times do |i|
      prediction = cnn.predict(X[i])
      actual = y[i]
      puts "Sample #{i}: Predicted #{prediction}, Actual #{actual}"
    end
  end
end
```

## 🎯 Training and Optimization

### 6. Training Techniques

Advanced training methods:

```ruby
class TrainingTechniques
  def self.learning_rate_schedules
    puts "Learning Rate Schedules:"
    puts "=" * 40
    
    schedules = [
      {
        name: "Fixed Learning Rate",
        description: "Constant learning rate throughout training",
        formula: "η(t) = η₀",
        pros: ["Simple", "Predictable"],
        cons: ["May not converge optimally", "Too fast/slow at different stages"]
      },
      {
        name: "Step Decay",
        description: "Reduce learning rate at specific epochs",
        formula: "η(t) = η₀ × γ^⌊t/s⌋",
        pros: ["Simple to implement", "Good convergence"],
        cons: ["Requires tuning", "Abrupt changes"]
      },
      {
        name: "Exponential Decay",
        description: "Exponentially decreasing learning rate",
        formula: "η(t) = η₀ × e^(-kt)",
        pros: ["Smooth decay", "Theoretically sound"],
        cons: ["May decay too fast", "Requires tuning"]
      },
      {
        name: "Cosine Annealing",
        description: "Cosine-based learning rate schedule",
        formula: "η(t) = η_min + (η_max - η_min) × (1 + cos(πt/T))/2",
        pros: ["Smooth restarts", "Good for fine-tuning"],
        cons: ["Complex", "More parameters"]
      }
    ]
    
    schedules.each do |schedule|
      puts "#{schedule[:name]}:"
      puts "  Description: #{schedule[:description]}"
      puts "  Formula: #{schedule[:formula]}"
      puts "  Pros: #{schedule[:pros].join(', ')}"
      puts "  Cons: #{schedule[:cons].join(', ')}"
      puts
    end
  end
  
  def self.regularization_techniques
    puts "\nRegularization Techniques:"
    puts "=" * 40
    
    techniques = [
      {
        name: "L1 Regularization (Lasso)",
        description: "Add absolute value of weights to loss",
        formula: "L = Loss + λ × Σ|w|",
        effect: "Sparsity, feature selection",
        use_case: "Feature selection, sparse models"
      },
      {
        name: "L2 Regularization (Ridge)",
        description: "Add squared weights to loss",
        formula: "L = Loss + λ × Σw²",
        effect: "Weight decay, prevents large weights",
        use_case: "General purpose regularization"
      },
      {
        name: "Elastic Net",
        description: "Combination of L1 and L2",
        formula: "L = Loss + λ₁ × Σ|w| + λ₂ × Σw²",
        effect: "Balance of L1 and L2 benefits",
        use_case: "When both sparsity and weight control needed"
      },
      {
        name: "Dropout",
        description: "Randomly disable neurons during training",
        formula: "Random mask during forward pass",
        effect: "Prevents co-adaptation, ensemble effect",
        use_case: "Preventing overfitting in deep networks"
      },
      {
        name: "Batch Normalization",
        description: "Normalize layer inputs",
        formula: "x̂ = (x - μ) / √(σ² + ε)",
        effect: "Stabilize training, faster convergence",
        use_case: "Deep networks, training stability"
      }
    ]
    
    techniques.each do |technique|
      puts "#{technique[:name]}:"
      puts "  Description: #{technique[:description]}"
      puts "  Formula: #{technique[:formula]}"
      puts "  Effect: #{technique[:effect]}"
      puts "  Use case: #{technique[:use_case]}"
      puts
    end
  end
  
  def self.optimization_algorithms
    puts "\nOptimization Algorithms:"
    puts "=" * 40
    
    algorithms = [
      {
        name: "SGD (Stochastic Gradient Descent)",
        description: "Update weights using gradient of single sample",
        formula: "w = w - η × ∇L",
        pros: ["Simple", "Memory efficient"],
        cons: ["Noisy updates", "Slow convergence"]
      },
      {
        name: "SGD with Momentum",
        description: "Add momentum to smooth updates",
        formula: "v = βv - η × ∇L, w = w + v",
        pros: ["Faster convergence", "Smooths noise"],
        cons: ["Extra parameter", "Less stable"]
      },
      {
        name: "Adam (Adaptive Moment Estimation)",
        description: "Adaptive learning rates with momentum",
        formula: "Complex (uses first and second moments)",
        pros: ["Fast convergence", "Adaptive rates"],
        cons: ["Memory intensive", "May overfit"]
      },
      {
        name: "RMSprop",
        description: "Adaptive learning rates based on gradient magnitude",
        formula: "w = w - η × ∇L / √(E[g²] + ε)",
        pros: ["Handles varying gradients", "Good for RNNs"],
        cons: ["Hyperparameter sensitive"]
      },
      {
        name: "AdaGrad",
        description: "Adaptive learning rates based on accumulated gradients",
        formula: "w = w - η × ∇L / √(G + ε)",
        pros: ["Adaptive per-parameter", "Good for sparse data"],
        cons: ["Learning rate decay", "May stop learning"]
      }
    ]
    
    algorithms.each do |algorithm|
      puts "#{algorithm[:name]}:"
      puts "  Description: #{algorithm[:description]}"
      puts "  Formula: #{algorithm[:formula]}"
      puts "  Pros: #{algorithm[:pros].join(', ')}"
      puts "  Cons: #{algorithm[:cons].join(', ')}"
      puts
    end
  end
  
  # Run training techniques examples
  learning_rate_schedules
  regularization_techniques
  optimization_algorithms
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Activation Functions**: Implement various activation functions
2. **Simple NN**: Build basic neural network
3. **Forward Pass**: Implement forward propagation
4. **Backward Pass**: Implement backpropagation

### Intermediate Exercises

1. **MLP**: Create multi-layer perceptron
2. **CNN**: Implement convolutional layer
3. **Training**: Add training loop with loss
4. **Regularization**: Implement dropout and L2

### Advanced Exercises

1. **Optimizers**: Implement Adam optimizer
2. **Batch Norm**: Add batch normalization
3. **Advanced CNN**: Build complete CNN architecture
4. **Real Data**: Train on actual datasets

---

## 🎯 Summary

Neural Networks in Ruby provide:

- **Neural Fundamentals** - Core concepts and architecture
- **Activation Functions** - Non-linear transformations
- **Network Implementation** - Simple and MLP networks
- **CNN** - Convolutional neural networks
- **Training Techniques** - Optimization and regularization
- **Practical Code** - Working neural network implementations

Master these concepts to build sophisticated AI models in Ruby!
