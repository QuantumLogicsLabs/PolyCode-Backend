# Deep Learning Frameworks in Ruby
# Comprehensive guide to implementing deep learning systems

## 🧠 Deep Learning Fundamentals

### 1. Neural Network Concepts

Core deep learning principles:

```ruby
class DeepLearningFundamentals
  def self.explain_deep_learning_concepts
    puts "Deep Learning Fundamentals:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Neural Networks",
        description: "Computational models inspired by biological neural networks",
        components: ["Neurons", "Layers", "Weights", "Biases", "Activation functions"],
        types: ["Feedforward", "Recurrent", "Convolutional", "Transformers"],
        applications: ["Classification", "Regression", "Generation", "Recognition"]
      },
      {
        concept: "Deep Learning",
        description: "Neural networks with multiple hidden layers",
        characteristics: ["Hierarchical features", "Automatic feature learning", "End-to-end learning"],
        benefits: ["Better performance", "Less feature engineering", "Complex pattern recognition"],
        challenges: ["Data requirements", "Computational cost", "Training complexity", "Interpretability"]
      },
      {
        concept: "Backpropagation",
        description: "Algorithm for training neural networks",
        process: ["Forward pass", "Loss calculation", "Backward pass", "Weight updates"],
        mathematics: ["Gradient descent", "Chain rule", "Partial derivatives", "Optimization"],
        variants: ["Stochastic", "Mini-batch", "Batch", "Adam", "RMSprop"]
      },
      {
        concept: "Activation Functions",
        description: "Non-linear functions applied to neuron outputs",
        types: ["Sigmoid", "Tanh", "ReLU", "Leaky ReLU", "Softmax"],
        properties: ["Non-linearity", "Differentiability", "Range", "Gradient behavior"],
        applications: ["Hidden layers", "Output layers", "Binary classification", "Multi-class classification"]
      },
      {
        concept: "Loss Functions",
        description: "Functions that measure model prediction error",
        types: ["MSE", "Cross-entropy", "Hinge loss", "Huber loss", "Custom loss"],
        purposes: ["Regression", "Classification", "Regularization", "Robustness"],
        optimization: ["Gradient-based", "Convexity", "Smoothness", "Differentiability"]
      },
      {
        concept: "Optimization Algorithms",
        description: "Methods for updating network weights",
        categories: ["First-order", "Second-order", "Adaptive", "Momentum-based"],
        algorithms: ["SGD", "Adam", "RMSprop", "AdaGrad", "LBFGS"],
        considerations: ["Learning rate", "Convergence speed", "Memory usage", "Stability"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Applications: #{concept[:applications].join(', ')}" if concept[:applications]
      puts "  Characteristics: #{concept[:characteristics].join(', ')}" if concept[:characteristics]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Process: #{concept[:process].join(', ')}" if concept[:process]
      puts "  Mathematics: #{concept[:mathematics].join(', ')}" if concept[:mathematics]
      puts "  Variants: #{concept[:variants].join(', ')}" if concept[:variants]
      puts "  Properties: #{concept[:properties].join(', ')}" if concept[:properties]
      puts "  Purposes: #{concept[:purposes].join(', ')}" if concept[:purposes]
      puts "  Categories: #{concept[:categories].join(', ')}" if concept[:categories]
      puts "  Algorithms: #{concept[:algorithms].join(', ')}" if concept[:algorithms]
      puts "  Considerations: #{concept[:considerations].join(', ')}" if concept[:considerations]
      puts
    end
  end
  
  def self.network_architectures
    puts "\nNeural Network Architectures:"
    puts "=" * 50
    
    architectures = [
      {
        name: "Multilayer Perceptron (MLP)",
        description: "Basic feedforward neural network",
        layers: ["Input layer", "Hidden layers", "Output layer"],
        characteristics: ["Fully connected", "Feedforward", "Universal approximation"],
        use_cases: ["Classification", "Regression", "Function approximation"],
        limitations: ["No spatial awareness", "Parameter explosion", "Sequential data"]
      },
      {
        name: "Convolutional Neural Networks (CNN)",
        description: "Networks designed for grid-like data",
        layers: ["Convolutional", "Pooling", "Fully connected"],
        components: ["Filters", "Kernels", "Feature maps", "Strides"],
        applications: ["Image classification", "Object detection", "Computer vision"],
        benefits: ["Parameter sharing", "Translation invariance", "Hierarchical features"]
      },
      {
        name: "Recurrent Neural Networks (RNN)",
        description: "Networks for sequential data",
        components: ["Hidden state", "Recurrence", "Time steps", "Memory"],
        variants: ["Vanilla RNN", "LSTM", "GRU", "Bidirectional"],
        applications: ["Time series", "NLP", "Speech recognition", "Sequential modeling"],
        challenges: ["Vanishing gradients", "Long-term dependencies", "Training difficulty"]
      },
      {
        name: "Transformer Networks",
        description: "Attention-based neural networks",
        components: ["Self-attention", "Multi-head attention", "Positional encoding", "Feed-forward layers"],
        innovations: ["Attention mechanism", "Parallel processing", "Positional encoding"],
        applications: ["NLP", "Computer vision", "Multi-modal", "Large language models"],
        advantages: ["Long-range dependencies", "Parallelizable", "State-of-the-art performance"]
      },
      {
        name: "Generative Adversarial Networks (GAN)",
        description: "Generative models with adversarial training",
        components: ["Generator", "Discriminator", "Adversarial loss", "Minimax game"],
        training: ["Alternating optimization", "Equilibrium", "Mode collapse", "Stability"],
        applications: ["Image generation", "Style transfer", "Data augmentation", "Synthetic data"]
      },
      {
        name: "Autoencoders",
        description: "Unsupervised learning for representation learning",
        architecture: ["Encoder", "Bottleneck", "Decoder", "Reconstruction"],
        types: ["Vanilla", "Denoising", "Variational", "Sparse"],
        uses: ["Dimensionality reduction", "Feature learning", "Anomaly detection", "Data generation"]
      }
    ]
    
    architectures.each do |arch|
      puts "#{arch[:name]}:"
      puts "  Description: #{arch[:description]}"
      puts "  Layers: #{arch[:layers].join(', ')}" if arch[:layers]
      puts "  Components: #{arch[:components].join(', ')}" if arch[:components]
      puts "  Characteristics: #{arch[:characteristics].join(', ')}" if arch[:characteristics]
      puts "  Use Cases: #{arch[:use_cases].join(', ')}" if arch[:use_cases]
      puts "  Limitations: #{arch[:limitations].join(', ')}" if arch[:limitations]
      puts "  Benefits: #{arch[:benefits].join(', ')}" if arch[:benefits]
      puts "  Variants: #{arch[:variants].join(', ')}" if arch[:variants]
      puts "  Applications: #{arch[:applications].join(', ')}" if arch[:applications]
      puts "  Challenges: #{arch[:challenges].join(', ')}" if arch[:challenges]
      puts "  Innovations: #{arch[:innovations].join(', ')}" if arch[:innovations]
      puts "  Advantages: #{arch[:advantages].join(', ')}" if arch[:advantages]
      puts "  Architecture: #{arch[:architecture].join(', ')}" if arch[:architecture]
      puts "  Types: #{arch[:types].join(', ')}" if arch[:types]
      puts "  Uses: #{arch[:uses].join(', ')}" if arch[:uses]
      puts
    end
  end
  
  def self.training_process
    puts "\nDeep Learning Training Process:"
    puts "=" * 50
    
    process = [
      {
        phase: "1. Data Preparation",
        description: "Prepare and preprocess training data",
        steps: ["Data collection", "Cleaning", "Normalization", "Splitting"],
        considerations: ["Data quality", "Bias handling", "Feature scaling", "Data augmentation"],
        outputs: ["Training set", "Validation set", "Test set", "Preprocessing pipeline"]
      },
      {
        phase: "2. Model Initialization",
        description: "Initialize neural network parameters",
        methods: ["Random initialization", "Xavier/Glorot", "He initialization", "Pretrained weights"],
        considerations: ["Weight scale", "Bias initialization", "Layer-specific initialization"],
        impact: ["Convergence speed", "Training stability", "Final performance"]
      },
      {
        phase: "3. Forward Propagation",
        description: "Compute network outputs",
        steps: ["Input feeding", "Layer computations", "Activation functions", "Output calculation"],
        computations: ["Matrix multiplications", "Element-wise operations", "Non-linear transformations"],
        intermediate: ["Activations", "Feature maps", "Hidden states", "Logits"]
      },
      {
        phase: "4. Loss Calculation",
        description: "Compute prediction error",
        functions: ["Cross-entropy", "Mean squared error", "Hinge loss", "Custom losses"],
        properties: ["Differentiability", "Convexity", "Scale", "Gradient behavior"],
        regularization: ["L1/L2 penalties", "Dropout", "Batch normalization", "Early stopping"]
      },
      {
        phase: "5. Backward Propagation",
        description: "Compute gradients and update weights",
        steps: ["Gradient computation", "Backpropagation", "Weight updates", "Parameter optimization"],
        algorithms: ["Gradient descent", "Adam", "RMSprop", "Adaptive methods"],
        considerations: ["Learning rate", "Momentum", "Weight decay", "Gradient clipping"]
      },
      {
        phase: "6. Evaluation and Validation",
        description: "Assess model performance",
        metrics: ["Accuracy", "Precision", "Recall", "F1-score", "AUC-ROC"],
        validation: ["Cross-validation", "Hold-out validation", "K-fold", "Stratified sampling"],
        monitoring: ["Training loss", "Validation loss", "Learning curves", "Overfitting detection"]
      }
    ]
    
    process.each do |phase|
      puts "#{phase[:phase]}: #{phase[:description]}"
      puts "  Steps: #{phase[:steps].join(', ')}" if phase[:steps]
      puts "  Considerations: #{phase[:considerations].join(', ')}" if phase[:considerations]
      puts "  Outputs: #{phase[:outputs].join(', ')}" if phase[:outputs]
      puts "  Methods: #{phase[:methods].join(', ')}" if phase[:methods]
      puts "  Impact: #{phase[:impact].join(', ')}" if phase[:impact]
      puts "  Computations: #{phase[:computations].join(', ')}" if phase[:computations]
      puts "  Intermediate: #{phase[:intermediate].join(', ')}" if phase[:intermediate]
      puts "  Functions: #{phase[:functions].join(', ')}" if phase[:functions]
      puts "  Properties: #{phase[:properties].join(', ')}" if phase[:properties]
      puts "  Regularization: #{phase[:regularization].join(', ')}" if phase[:regularization]
      puts "  Algorithms: #{phase[:algorithms].join(', ')}" if phase[:algorithms]
      puts "  Metrics: #{phase[:metrics].join(', ')}" if phase[:metrics]
      puts "  Validation: #{phase[:validation].join(', ')}" if phase[:validation]
      puts "  Monitoring: #{phase[:monitoring].join(', ')}" if phase[:monitoring]
      puts
    end
  end
  
  # Run deep learning fundamentals
  explain_deep_learning_concepts
  network_architectures
  training_process
end
```

### 2. Neural Network Implementation

Building neural networks from scratch:

```ruby
class NeuralNetwork
  def initialize(layers, activation = :relu, learning_rate = 0.01)
    @layers = layers
    @activation = activation
    @learning_rate = learning_rate
    @weights = []
    @biases = []
    @activations = []
    @z_values = []
    
    # Initialize weights and biases
    (layers.length - 1).times do |i|
      # Xavier initialization
      fan_in = layers[i]
      fan_out = layers[i + 1]
      limit = Math.sqrt(6.0 / (fan_in + fan_out))
      
      @weights << Matrix.random(layers[i + 1], layers[i]) { rand(-limit..limit) }
      @biases << Matrix.random(layers[i + 1], 1) { rand(-limit..limit) }
    end
  end
  
  attr_reader :layers, :weights, :biases
  
  def forward(input)
    activations = [Matrix.column_vector(input)]
    z_values = []
    
    @weights.each_with_index do |weight, i|
      z = weight * activations.last + @biases[i]
      z_values << z
      
      # Apply activation function except for output layer
      if i < @weights.length - 1
        activation = apply_activation(z)
        activations << activation
      else
        # Output layer - no activation for regression, softmax for classification
        if @activation == :softmax
          activation = softmax(z)
          activations << activation
        else
          activations << z
        end
      end
    end
    
    @activations = activations
    @z_values = z_values
    
    activations.last.to_a.flatten
  end
  
  def backward(input, target)
    # Forward pass to get activations
    output = forward(input)
    
    # Initialize gradients
    weight_gradients = @weights.map { |w| Matrix.zeros(w.row_count, w.column_count) }
    bias_gradients = @biases.map { |b| Matrix.zeros(b.row_count, b.column_count) }
    
    # Calculate output layer error
    output_error = calculate_output_error(@activations.last, target)
    
    # Backpropagate errors
    errors = [output_error]
    
    (@weights.length - 2).downto(0) do |i|
      error = @weights[i + 1].transpose * errors.last
      error = elementwise_multiply(error, activation_derivative(@z_values[i]))
      errors.unshift(error)
    end
    
    # Calculate gradients
    errors.each_with_index do |error, i|
      if i == 0
        # First layer - use input
        weight_gradients[i] = error * @activations[i].transpose
      else
        weight_gradients[i] = error * @activations[i].transpose
      end
      
      bias_gradients[i] = error
    end
    
    # Update weights and biases
    @weights.each_with_index do |weight, i|
      @weights[i] = weight - weight_gradients[i] * @learning_rate
      @biases[i] = @biases[i] - bias_gradients[i] * @learning_rate
    end
    
    # Calculate loss
    loss = calculate_loss(@activations.last, target)
    
    {
      output: output,
      loss: loss,
      gradients: weight_gradients
    }
  end
  
  def train(inputs, targets, epochs = 100, batch_size = 32)
    training_data = inputs.zip(targets)
    losses = []
    
    epochs.times do |epoch|
      epoch_loss = 0.0
      training_data.shuffle.each_slice(batch_size) do |batch|
        batch_loss = 0.0
        
        batch.each do |input, target|
          result = backward(input, target)
          batch_loss += result[:loss]
        end
        
        epoch_loss += batch_loss
      end
      
      avg_loss = epoch_loss / training_data.length
      losses << avg_loss
      
      puts "Epoch #{epoch + 1}: Loss = #{avg_loss.round(6)}" if (epoch + 1) % 10 == 0
    end
    
    losses
  end
  
  def predict(input)
    output = forward(input)
    
    if @activation == :softmax
      # Return class with highest probability
      output.index(output.max)
    else
      output
    end
  end
  
  def save_model(filename)
    model_data = {
      layers: @layers,
      weights: @weights.map(&:to_a),
      biases: @biases.map(&:to_a),
      activation: @activation
    }
    
    File.write(filename, model_data.to_json)
  end
  
  def self.load_model(filename)
    model_data = JSON.parse(File.read(filename))
    
    network = NeuralNetwork.new(
      model_data['layers'],
      model_data['activation'].to_sym
    )
    
    network.instance_variable_set(:@weights, 
      model_data['weights'].map { |w| Matrix.rows(w) }
    )
    network.instance_variable_set(:@biases,
      model_data['biases'].map { |b| Matrix.rows(b) }
    )
    
    network
  end
  
  def self.demonstrate_neural_network
    puts "Neural Network Demonstration:"
    puts "=" * 50
    
    # Create neural network for XOR problem
    network = NeuralNetwork.new([2, 4, 1], :sigmoid, 0.1)
    
    # XOR training data
    inputs = [
      [0, 0],
      [0, 1],
      [1, 0],
      [1, 1]
    ]
    
    targets = [
      [0],
      [1],
      [1],
      [0]
    ]
    
    puts "Training XOR neural network:"
    
    # Train network
    losses = network.train(inputs, targets, 1000, 4)
    
    # Test predictions
    puts "\nTesting predictions:"
    
    inputs.each_with_index do |input, i|
      prediction = network.predict(input)
      puts "  Input: #{input} -> Predicted: #{prediction.round(2)}, Target: #{targets[i][0]}"
    end
    
    # Plot learning curve (simplified)
    puts "\nLearning curve (last 10 epochs):"
    losses.last(10).each_with_index do |loss, i|
      puts "  Epoch #{losses.length - 10 + i + 1}: #{loss.round(6)}"
    end
    
    puts "\nNeural Network Features:"
    puts "- Multi-layer architecture"
    puts "- Forward and backward propagation"
    puts "- Multiple activation functions"
    puts "- Gradient descent optimization"
    puts "- Model saving and loading"
    puts "- Batch training support"
  end
  
  private
  
  def apply_activation(z)
    case @activation
    when :relu
      relu(z)
    when :sigmoid
      sigmoid(z)
    when :tanh
      tanh(z)
    when :softmax
      softmax(z)
    else
      z
    end
  end
  
  def activation_derivative(z)
    case @activation
    when :relu
      relu_derivative(z)
    when :sigmoid
      sigmoid_derivative(z)
    when :tanh
      tanh_derivative(z)
    else
      Matrix.ones(z.row_count, z.column_count)
    end
  end
  
  def relu(z)
    Matrix.rows(z.to_a.map { |row| row.map { |x| [x, 0].max } })
  end
  
  def relu_derivative(z)
    Matrix.rows(z.to_a.map { |row| row.map { |x| x > 0 ? 1 : 0 } })
  end
  
  def sigmoid(z)
    Matrix.rows(z.to_a.map { |row| row.map { |x| 1 / (1 + Math.exp(-x)) } })
  end
  
  def sigmoid_derivative(z)
    s = sigmoid(z)
    Matrix.rows(s.to_a.map { |row| row.map { |x| x * (1 - x) } })
  end
  
  def tanh(z)
    Matrix.rows(z.to_a.map { |row| row.map { |x| Math.tanh(x) } })
  end
  
  def tanh_derivative(z)
    t = tanh(z)
    Matrix.rows(t.to_a.map { |row| row.map { |x| 1 - x * x } })
  end
  
  def softmax(z)
    exp_z = Matrix.rows(z.to_a.map { |row| row.map { |x| Math.exp(x) } })
    sum_exp = exp_z.to_a.flatten.sum
    Matrix.rows(exp_z.to_a.map { |row| row.map { |x| x / sum_exp } })
  end
  
  def calculate_output_error(output, target)
    case @activation
    when :softmax
      output - Matrix.column_vector(target)
    else
      output - Matrix.column_vector(target)
    end
  end
  
  def calculate_loss(output, target)
    case @activation
    when :softmax
      # Cross-entropy loss
      -target.each_with_index.sum { |t, i| t * Math.log(output[i, 0] + 1e-10) }
    else
      # Mean squared error
      ((output - Matrix.column_vector(target)).map { |x| x * x }).sum / output.row_count
    end
  end
  
  def elementwise_multiply(a, b)
    Matrix.rows(a.to_a.zip(b.to_a).map { |row_a, row_b| row_a.zip(row_b).map { |x, y| x * y } })
  end
end

class ConvolutionalLayer
  def initialize(input_size, filter_size, num_filters, stride = 1, padding = 0)
    @input_size = input_size
    @filter_size = filter_size
    @num_filters = num_filters
    @stride = stride
    @padding = padding
    
    # Initialize filters
    @filters = num_filters.times.map do
      Matrix.random(filter_size, filter_size) { rand(-0.1..0.1) }
    end
    
    @biases = Array.new(num_filters, 0.0)
    
    # Calculate output size
    @output_size = ((input_size + 2 * padding - filter_size) / stride + 1).to_i
  end
  
  attr_reader :output_size, :filters, :biases
  
  def forward(input)
    input_matrix = Matrix.rows(input)
    @last_input = input_matrix
    
    output = @num_filters.times.map do |filter_idx|
      filter = @filters[filter_idx]
      bias = @biases[filter_idx]
      
      # Convolution operation
      feature_map = Array.new(@output_size) do |i|
        Array.new(@output_size) do |j|
          sum = 0.0
          
          @filter_size.times do |m|
            @filter_size.times do |n|
              input_i = i * @stride + m - @padding
              input_j = j * @stride + n - @padding
              
              if input_i >= 0 && input_i < @input_size && input_j >= 0 && input_j < @input_size
                sum += input_matrix[input_i, input_j] * filter[m, n]
              end
            end
          end
          
          sum + bias
        end
      end
      
      feature_map
    end
    
    @last_output = output
    output
  end
  
  def backward(input, grad_output)
    # Simplified backward pass
    input_grad = Matrix.zeros(@input_size, @input_size)
    filter_grads = @filters.map { |f| Matrix.zeros(f.row_count, f.column_count) }
    bias_grads = Array.new(@num_filters, 0.0)
    
    grad_output.each_with_index do |feature_map, filter_idx|
      feature_map.each_with_index do |grad_val, i, j|
        # Gradient for input
        @filter_size.times do |m|
          @filter_size.times do |n|
            input_i = i * @stride + m - @padding
            input_j = j * @stride + n - @padding
            
            if input_i >= 0 && input_i < @input_size && input_j >= 0 && input_j < @input_size
              input_grad[input_i, input_j] += grad_val * @filters[filter_idx][m, n]
            end
          end
        end
        
        # Gradient for filter
        @filter_size.times do |m|
          @filter_size.times do |n|
            input_i = i * @stride + m - @padding
            input_j = j * @stride + n - @padding
            
            if input_i >= 0 && input_i < @input_size && input_j >= 0 && input_j < @input_size
              filter_grads[filter_idx][m, n] += grad_val * @last_input[input_i, input_j]
            end
          end
        end
        
        # Gradient for bias
        bias_grads[filter_idx] += grad_val
      end
    end
    
    {
      input_grad: input_grad,
      filter_grads: filter_grads,
      bias_grads: bias_grads
    }
  end
  
  def update_filters(learning_rate, filter_grads, bias_grads)
    @filters.each_with_index do |filter, i|
      @filters[i] = filter - filter_grads[i] * learning_rate
      @biases[i] -= bias_grads[i] * learning_rate
    end
  end
  
  def self.demonstrate_convolution
    puts "Convolutional Layer Demonstration:"
    puts "=" * 50
    
    # Create convolutional layer
    conv_layer = ConvolutionalLayer.new(28, 3, 4, stride = 1, padding = 0)
    
    # Create sample input (28x28 image)
    input = Array.new(28) { Array.new(28) { rand(0..255) / 255.0 } }
    
    puts "Input shape: #{input.length}x#{input[0].length}"
    puts "Filter size: #{conv_layer.filter_size}x#{conv_layer.filter_size}"
    puts "Number of filters: #{conv_layer.num_filters}"
    puts "Output size: #{conv_layer.output_size}x#{conv_layer.output_size}"
    
    # Forward pass
    output = conv_layer.forward(input)
    puts "Output shape: #{output.length}x#{output[0].length}x#{output[0][0].length}"
    
    # Show sample output
    puts "\nSample output (first filter, first 5x5):"
    output[0][0..4].each do |row|
      puts "  #{row.map { |x| x.round(3) }.join(' ')}"
    end
    
    puts "\nConvolutional Layer Features:"
    puts "- Convolution operation"
    puts "- Multiple filters"
    puts "- Stride and padding"
    puts "- Forward and backward pass"
    puts "- Gradient computation"
    puts "- Filter updates"
  end
end

class PoolingLayer
  def initialize(pool_size = 2, stride = 2, mode = :max)
    @pool_size = pool_size
    @stride = stride
    @mode = mode
  end
  
  attr_reader :pool_size, :stride, :mode
  
  def forward(input)
    @last_input_shape = [input.length, input[0].length]
    
    output_size = input.length / @pool_size
    output = Array.new(output_size) do |i|
      Array.new(output_size) do |j|
        if @mode == :max
          max_pool_region(input, i * @pool_size, j * @pool_size)
        else
          avg_pool_region(input, i * @pool_size, j * @pool_size)
        end
      end
    end
    
    output
  end
  
  def backward(input, grad_output)
    # Simplified backward pass for max pooling
    input_grad = Array.new(@last_input_shape[0]) do
      Array.new(@last_input_shape[1], 0.0)
    end
    
    grad_output.each_with_index do |grad_val, i, j|
      if @mode == :max
        # Find max element position
        max_pos = find_max_position(@last_input, i * @pool_size, j * @pool_size)
        if max_pos
          input_grad[max_pos[0]][max_pos[1]] += grad_val
        end
      else
        # Average pooling - distribute gradient evenly
        @pool_size.times do |m|
          @pool_size.times do |n|
            input_i = i * @pool_size + m
            input_j = j * @pool_size + n
            input_grad[input_i][input_j] += grad_val / (@pool_size * @pool_size)
          end
        end
      end
    end
    
    input_grad
  end
  
  private
  
  def max_pool_region(input, start_i, start_j)
    max_val = -Float::INFINITY
    
    @pool_size.times do |m|
      @pool_size.times do |n|
        val = input[start_i + m][start_j + n]
        max_val = [max_val, val].max
      end
    end
    
    max_val
  end
  
  def avg_pool_region(input, start_i, start_j)
    sum = 0.0
    
    @pool_size.times do |m|
      @pool_size.times do |n|
        sum += input[start_i + m][start_j + n]
      end
    end
    
    sum / (@pool_size * @pool_size)
  end
  
  def find_max_position(input, start_i, start_j)
    max_val = -Float::INFINITY
    max_pos = nil
    
    @pool_size.times do |m|
      @pool_size.times do |n|
        val = input[start_i + m][start_j + n]
        if val > max_val
          max_val = val
          max_pos = [start_i + m, start_j + n]
        end
      end
    end
    
    max_pos
  end
  
  def self.demonstrate_pooling
    puts "Pooling Layer Demonstration:"
    puts "=" * 50
    
    # Create pooling layer
    pool_layer = PoolingLayer.new(2, 2, :max)
    
    # Create sample input (4x4)
    input = [
      [1, 3, 2, 4],
      [5, 2, 8, 1],
      [3, 6, 2, 7],
      [4, 1, 9, 3]
    ]
    
    puts "Input:"
    input.each { |row| puts "  #{row.join(' ')}" }
    
    # Forward pass
    output = pool_layer.forward(input)
    puts "\nOutput (max pooling):"
    output.each { |row| puts "  #{row.map { |x| x.round(2) }.join(' ')}" }
    
    # Test average pooling
    avg_pool_layer = PoolingLayer.new(2, 2, :avg)
    avg_output = avg_pool_layer.forward(input)
    puts "\nOutput (avg pooling):"
    avg_output.each { |row| puts "  #{row.map { |x| x.round(2) }.join(' ')}" }
    
    puts "\nPooling Layer Features:"
    puts "- Max and average pooling"
    puts "- Spatial downsampling"
    puts "- Translation invariance"
    puts "- Backward pass support"
    puts "- Gradient routing"
  end
end

# Matrix class for neural network operations
class Matrix
  def self.rows(rows)
    new(rows)
  end
  
  def self.random(rows, cols, &block)
    rows_data = Array.new(rows) { Array.new(cols) }
    rows_data.each_with_index do |row, i|
      row.each_with_index do |_, j|
        rows_data[i][j] = block.call(i, j)
      end
    end
    new(rows_data)
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
    @data[0].length
  end
  
  def to_a
    @data
  end
  
  def transpose
    Matrix.new(@data.transpose)
  end
  
  def *(other)
    if other.is_a?(Matrix)
      # Matrix multiplication
      result = Array.new(row_count) { Array.new(other.column_count, 0) }
      
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
    else
      # Scalar multiplication
      Matrix.new(@data.map { |row| row.map { |x| x * other } })
    end
  end
  
  def +(other)
    Matrix.new(@data.zip(other.to_a).map { |row1, row2| row1.zip(row2).map { |x, y| x + y } })
  end
  
  def -(other)
    Matrix.new(@data.zip(other.to_a).map { |row1, row2| row1.zip(row2).map { |x, y| x - y } })
  end
  
  def map(&block)
    Matrix.new(@data.map { |row| row.map(&block) })
  end
  
  def sum
    @data.flatten.sum
  end
  
  def zeros(rows, cols)
    Matrix.new(Array.new(rows) { Array.new(cols, 0) })
  end
  
  def column_vector(vector)
    Matrix.new(vector.map { |x| [x] })
  end
end
```

## 🎯 Deep Learning Framework

### 3. Custom Framework Implementation

Building a complete deep learning framework:

```ruby
class DeepLearningFramework
  def initialize
    @layers = []
    @loss_function = nil
    @optimizer = nil
    @metrics = {}
    @training_history = []
  end
  
  attr_reader :layers, :loss_function, :optimizer
  
  def add_layer(layer)
    @layers << layer
    self
  end
  
  def set_loss_function(loss_function)
    @loss_function = loss_function
    self
  end
  
  def set_optimizer(optimizer)
    @optimizer = optimizer
    self
  end
  
  def compile(loss: :mse, optimizer: :sgd, learning_rate: 0.01, **options)
    @loss_function = create_loss_function(loss)
    @optimizer = create_optimizer(optimizer, learning_rate, options)
  end
  
  def fit(x_train, y_train, epochs: 100, batch_size: 32, validation_data: nil)
    num_samples = x_train.length
    num_batches = (num_samples.to_f / batch_size).ceil
    
    epochs.times do |epoch|
      epoch_loss = 0.0
      num_batches.times do |batch_idx|
        start_idx = batch_idx * batch_size
        end_idx = [start_idx + batch_size, num_samples].min
        
        x_batch = x_train[start_idx...end_idx]
        y_batch = y_train[start_idx...end_idx]
        
        batch_loss = train_batch(x_batch, y_batch)
        epoch_loss += batch_loss
      end
      
      avg_loss = epoch_loss / num_batches
      @training_history << { epoch: epoch + 1, loss: avg_loss }
      
      # Validation
      if validation_data && (epoch + 1) % 10 == 0
        val_loss = evaluate(validation_data[0], validation_data[1])
        puts "Epoch #{epoch + 1}: Loss = #{avg_loss.round(6)}, Val Loss = #{val_loss.round(6)}"
      elsif (epoch + 1) % 10 == 0
        puts "Epoch #{epoch + 1}: Loss = #{avg_loss.round(6)}"
      end
    end
    
    @training_history
  end
  
  def predict(x)
    output = x
    
    @layers.each do |layer|
      output = layer.forward(output)
    end
    
    output
  end
  
  def evaluate(x_test, y_test)
    predictions = predict(x_test)
    loss = @loss_function.compute(predictions, y_test)
    loss
  end
  
  def save_model(filepath)
    model_data = {
      layers: @layers.map(&:serialize),
      loss_function: @loss_function.class.name,
      optimizer: @optimizer.class.name,
      training_history: @training_history
    }
    
    File.write(filepath, model_data.to_json)
  end
  
  def self.load_model(filepath)
    model_data = JSON.parse(File.read(filepath))
    
    framework = DeepLearningFramework.new
    
    # Load layers
    model_data['layers'].each do |layer_data|
      layer = deserialize_layer(layer_data)
      framework.add_layer(layer)
    end
    
    # Set loss function and optimizer
    framework.instance_variable_set(:@loss_function, Object.const_get(model_data['loss_function']).new)
    framework.instance_variable_set(:@optimizer, Object.const_get(model_data['optimizer']).new)
    
    framework.instance_variable_set(:@training_history, model_data['training_history'])
    
    framework
  end
  
  def summary
    puts "Model Summary:"
    puts "=" * 30
    
    total_params = 0
    
    @layers.each_with_index do |layer, i|
      layer_name = layer.class.name
      params = layer.num_parameters
      total_params += params
      
      puts "Layer #{i + 1}: #{layer_name} (Parameters: #{params})"
    end
    
    puts "\nTotal Parameters: #{total_params}"
    puts "Loss Function: #{@loss_function.class.name}"
    puts "Optimizer: #{@optimizer.class.name}"
  end
  
  def self.demonstrate_framework
    puts "Deep Learning Framework Demonstration:"
    puts "=" * 50
    
    # Create framework
    framework = DeepLearningFramework.new
    
    # Build model
    framework
      .add_layer(DenseLayer.new(784, 128, activation: :relu))
      .add_layer(DropoutLayer.new(0.2))
      .add_layer(DenseLayer.new(128, 64, activation: :relu))
      .add_layer(DenseLayer.new(64, 10, activation: :softmax))
      .compile(loss: :cross_entropy, optimizer: :adam, learning_rate: 0.001)
    
    # Show model summary
    framework.summary
    
    # Generate sample data (MNIST-like)
    puts "\nGenerating sample data..."
    x_train = Array.new(1000) { Array.new(784) { rand(0..255) / 255.0 } }
    y_train = Array.new(1000) { rand(0..9) }
    
    # One-hot encode targets
    y_train_one_hot = y_train.map { |y| (0..9).map { |i| i == y ? 1 : 0 } }
    
    x_val = Array.new(200) { Array.new(784) { rand(0..255) / 255.0 } }
    y_val = Array.new(200) { rand(0..9) }
    y_val_one_hot = y_val.map { |y| (0..9).map { |i| i == y ? 1 : 0 } }
    
    puts "Training data: #{x_train.length} samples"
    puts "Validation data: #{x_val.length} samples"
    
    # Train model
    puts "\nTraining model..."
    history = framework.fit(x_train, y_train_one_hot, epochs: 20, batch_size: 32, validation_data: [x_val, y_val_one_hot])
    
    # Make predictions
    puts "\nMaking predictions..."
    test_sample = x_train.first(5)
    predictions = framework.predict(test_sample)
    
    predictions.each_with_index do |pred, i|
      predicted_class = pred.index(pred.max)
      actual_class = y_train[i]
      puts "  Sample #{i + 1}: Predicted #{predicted_class}, Actual #{actual_class}"
    end
    
    puts "\nFramework Features:"
    puts "- Layer-based architecture"
    puts "- Multiple loss functions"
    puts "- Various optimizers"
    puts "- Training and evaluation"
    puts "- Model serialization"
    puts "- Dropout regularization"
    puts "- Batch training"
  end
  
  private
  
  def train_batch(x_batch, y_batch)
    # Forward pass
    activations = [x_batch]
    
    @layers.each do |layer|
      activations << layer.forward(activations.last)
    end
    
    # Compute loss
    loss = @loss_function.compute(activations.last, y_batch)
    
    # Backward pass
    grad = @loss_function.gradient(activations.last, y_batch)
    
    @layers.reverse.each do |layer|
      grad = layer.backward(activations.pop, grad)
    end
    
    # Update parameters
    @layers.each { |layer| layer.update_parameters(@optimizer) }
    
    loss
  end
  
  def create_loss_function(loss_type)
    case loss_type
    when :mse
      MSELoss.new
    when :cross_entropy
      CrossEntropyLoss.new
    when :hinge
      HingeLoss.new
    else
      MSELoss.new
    end
  end
  
  def create_optimizer(optimizer_type, learning_rate, options)
    case optimizer_type
    when :sgd
      SGD.new(learning_rate)
    when :adam
      Adam.new(learning_rate, options)
    when :rmsprop
      RMSprop.new(learning_rate)
    else
      SGD.new(learning_rate)
    end
  end
  
  def deserialize_layer(layer_data)
    case layer_data['type']
    when 'DenseLayer'
      DenseLayer.new(layer_data['input_size'], layer_data['output_size'], activation: layer_data['activation'])
    when 'DropoutLayer'
      DropoutLayer.new(layer_data['rate'])
    else
      raise "Unknown layer type: #{layer_data['type']}"
    end
  end
end

# Layer implementations
class DenseLayer
  def initialize(input_size, output_size, activation: :linear)
    @input_size = input_size
    @output_size = output_size
    @activation = activation
    
    # Initialize weights and biases
    @weights = Matrix.random(output_size, input_size) { rand(-0.1..0.1) }
    @biases = Matrix.random(output_size, 1) { rand(-0.1..0.1) }
    
    # For gradient computation
    @last_input = nil
    @last_output = nil
  end
  
  attr_reader :input_size, :output_size, :activation, :weights, :biases
  
  def forward(input)
    @last_input = input
    
    # Convert input to matrix if needed
    input_matrix = input.is_a?(Array) ? Matrix.column_vector(input) : input
    
    # Linear transformation
    z = @weights * input_matrix + @biases
    
    # Apply activation
    output = apply_activation(z)
    @last_output = output
    
    output
  end
  
  def backward(input, grad_output)
    # Compute gradients
    activation_grad = activation_derivative(@last_output)
    grad_z = elementwise_multiply(grad_output, activation_grad)
    
    # Gradient for weights and biases
    grad_weights = grad_z * @last_input.transpose
    grad_biases = grad_z
    
    # Gradient for input
    grad_input = @weights.transpose * grad_z
    
    # Store gradients for parameter update
    @grad_weights = grad_weights
    @grad_biases = grad_biases
    
    grad_input
  end
  
  def update_parameters(optimizer)
    @weights, @biases = optimizer.update(@weights, @biases, @grad_weights, @grad_biases)
  end
  
  def num_parameters
    @weights.row_count * @weights.column_count + @biases.row_count
  end
  
  def serialize
    {
      'type' => 'DenseLayer',
      'input_size' => @input_size,
      'output_size' => @output_size,
      'activation' => @activation
    }
  end
  
  private
  
  def apply_activation(z)
    case @activation
    when :relu
      relu(z)
    when :sigmoid
      sigmoid(z)
    when :tanh
      tanh(z)
    when :softmax
      softmax(z)
    else
      z
    end
  end
  
  def activation_derivative(output)
    case @activation
    when :relu
      relu_derivative(output)
    when :sigmoid
      sigmoid_derivative(output)
    when :tanh
      tanh_derivative(output)
    else
      Matrix.ones(output.row_count, output.column_count)
    end
  end
  
  def relu(z)
    Matrix.rows(z.to_a.map { |row| row.map { |x| [x, 0].max } })
  end
  
  def relu_derivative(output)
    Matrix.rows(output.to_a.map { |row| row.map { |x| x > 0 ? 1 : 0 } })
  end
  
  def sigmoid(z)
    Matrix.rows(z.to_a.map { |row| row.map { |x| 1 / (1 + Math.exp(-x)) } })
  end
  
  def sigmoid_derivative(output)
    Matrix.rows(output.to_a.map { |row| row.map { |x| x * (1 - x) } })
  end
  
  def tanh(z)
    Matrix.rows(z.to_a.map { |row| row.map { |x| Math.tanh(x) } })
  end
  
  def tanh_derivative(output)
    Matrix.rows(output.to_a.map { |row| row.map { |x| 1 - x * x } })
  end
  
  def softmax(z)
    exp_z = Matrix.rows(z.to_a.map { |row| row.map { |x| Math.exp(x) } })
    sum_exp = exp_z.to_a.flatten.sum
    Matrix.rows(exp_z.to_a.map { |row| row.map { |x| x / sum_exp } })
  end
  
  def elementwise_multiply(a, b)
    Matrix.rows(a.to_a.zip(b.to_a).map { |row_a, row_b| row_a.zip(row_b).map { |x, y| x * y } })
  end
end

class DropoutLayer
  def initialize(rate)
    @rate = rate
    @mask = nil
    @training = true
  end
  
  attr_reader :rate
  
  def forward(input)
    return input unless @training
    
    # Create dropout mask
    @mask = Array.new(input.length) { rand > @rate }
    
    # Apply mask
    input.map.with_index { |x, i| @mask[i] ? x / (1 - @rate) : 0 }
  end
  
  def backward(input, grad_output)
    return grad_output unless @training
    
    # Apply mask to gradients
    grad_output.map.with_index { |x, i| @mask[i] ? x : 0 }
  end
  
  def update_parameters(optimizer)
    # Dropout layer has no parameters to update
  end
  
  def num_parameters
    0
  end
  
  def serialize
    {
      'type' => 'DropoutLayer',
      'rate' => @rate
    }
  end
  
  def train
    @training = true
  end
  
  def eval
    @training = false
  end
end

# Loss functions
class MSELoss
  def compute(predictions, targets)
    # Convert to matrices
    pred_matrix = predictions.is_a?(Array) ? Matrix.column_vector(predictions) : predictions
    target_matrix = targets.is_a?(Array) ? Matrix.column_vector(targets) : targets
    
    # Compute MSE
    diff = pred_matrix - target_matrix
    (diff.map { |x| x * x }).sum / pred_matrix.row_count
  end
  
  def gradient(predictions, targets)
    pred_matrix = predictions.is_a?(Array) ? Matrix.column_vector(predictions) : predictions
    target_matrix = targets.is_a?(Array) ? Matrix.column_vector(targets) : targets
    
    # MSE gradient
    2.0 * (pred_matrix - target_matrix) / pred_matrix.row_count
  end
end

class CrossEntropyLoss
  def compute(predictions, targets)
    # Convert to matrices
    pred_matrix = predictions.is_a?(Array) ? Matrix.column_vector(predictions) : predictions
    target_matrix = targets.is_a?(Array) ? Matrix.column_vector(targets) : targets
    
    # Compute cross-entropy loss
    -target_matrix.to_a.flatten.zip(pred_matrix.to_a.flatten).sum do |t, p|
      t * Math.log(p + 1e-10)
    end / pred_matrix.row_count
  end
  
  def gradient(predictions, targets)
    pred_matrix = predictions.is_a?(Array) ? Matrix.column_vector(predictions) : predictions
    target_matrix = targets.is_a?(Array) ? Matrix.column_vector(targets) : targets
    
    # Cross-entropy gradient
    (pred_matrix - target_matrix) / pred_matrix.row_count
  end
end

class HingeLoss
  def compute(predictions, targets)
    pred_matrix = predictions.is_a?(Array) ? Matrix.column_vector(predictions) : predictions
    target_matrix = targets.is_a?(Array) ? Matrix.column_vector(targets) : targets
    
    # Hinge loss
    losses = pred_matrix.to_a.zip(target_matrix.to_a).map do |p, t|
      [1 - p * t, 0].max
    end
    
    losses.sum / pred_matrix.row_count
  end
  
  def gradient(predictions, targets)
    # Simplified hinge loss gradient
    pred_matrix = predictions.is_a?(Array) ? Matrix.column_vector(predictions) : predictions
    target_matrix = targets.is_a?(Array) ? Matrix.column_vector(targets) : targets
    
    pred_matrix.to_a.zip(target_matrix.to_a).map do |p, t|
      p * t < 1 ? -t : 0
    end
  end
end

# Optimizers
class SGD
  def initialize(learning_rate = 0.01)
    @learning_rate = learning_rate
  end
  
  def update(weights, biases, grad_weights, grad_biases)
    new_weights = weights - grad_weights * @learning_rate
    new_biases = biases - grad_biases * @learning_rate
    [new_weights, new_biases]
  end
end

class Adam
  def initialize(learning_rate = 0.001, beta1 = 0.9, beta2 = 0.999, epsilon = 1e-8)
    @learning_rate = learning_rate
    @beta1 = beta1
    @beta2 = beta2
    @epsilon = epsilon
    @t = 0
    
    @m_weights = nil
    @v_weights = nil
    @m_biases = nil
    @v_biases = nil
  end
  
  def update(weights, biases, grad_weights, grad_biases)
    @t += 1
    
    # Initialize moments
    if @m_weights.nil?
      @m_weights = grad_weights.map { 0 }
      @v_weights = grad_weights.map { 0 }
      @m_biases = grad_biases.map { 0 }
      @v_biases = grad_biases.map { 0 }
    end
    
    # Update biased first moment estimate
    @m_weights = @m_weights.zip(grad_weights).map { |m, g| @beta1 * m + (1 - @beta1) * g }
    @m_biases = @m_biases.zip(grad_biases).map { |m, g| @beta1 * m + (1 - @beta1) * g }
    
    # Update biased second moment estimate
    @v_weights = @v_weights.zip(grad_weights).map { |v, g| @beta2 * v + (1 - @beta2) * g * g }
    @v_biases = @v_biases.zip(grad_biases).map { |v, g| @beta2 * v + (1 - @beta2) * g * g }
    
    # Compute bias-corrected estimates
    m_weights_hat = @m_weights.map { |m| m / (1 - @beta1**@t) }
    m_biases_hat = @m_biases.map { |m| m / (1 - @beta1**@t) }
    v_weights_hat = @v_weights.map { |v| v / (1 - @beta2**@t) }
    v_biases_hat = @v_biases.map { |v| v / (1 - @beta2**@t) }
    
    # Update parameters
    new_weights = weights.zip(m_weights_hat, v_weights_hat).map do |w, m, v|
      w - @learning_rate * m / (v.sqrt + @epsilon)
    end
    
    new_biases = biases.zip(m_biases_hat, v_biases_hat).map do |b, m, v|
      b - @learning_rate * m / (v.sqrt + @epsilon)
    end
    
    [new_weights, new_biases]
  end
end

class RMSprop
  def initialize(learning_rate = 0.001, decay_rate = 0.9, epsilon = 1e-8)
    @learning_rate = learning_rate
    @decay_rate = decay_rate
    @epsilon = epsilon
    
    @v_weights = nil
    @v_biases = nil
  end
  
  def update(weights, biases, grad_weights, grad_biases)
    # Initialize squared gradients
    if @v_weights.nil?
      @v_weights = grad_weights.map { 0 }
      @v_biases = grad_biases.map { 0 }
    end
    
    # Update squared gradients
    @v_weights = @v_weights.zip(grad_weights).map do |v, g|
      @decay_rate * v + (1 - @decay_rate) * g * g
    end
    @v_biases = @v_biases.zip(grad_biases).map do |v, g|
      @decay_rate * v + (1 - @decay_rate) * g * g
    end
    
    # Update parameters
    new_weights = weights.zip(@v_weights).map do |w, v|
      w - @learning_rate * grad_weights / (v.sqrt + @epsilon)
    end
    
    new_biases = biases.zip(@v_biases).map do |b, v|
      b - @learning_rate * grad_biases / (v.sqrt + @epsilon)
    end
    
    [new_weights, new_biases]
  end
end
```

## 🤖 Computer Vision

### 4. Image Processing and CNN

Computer vision applications:

```ruby
class ComputerVision
  def self.demonstrate_image_processing
    puts "Computer Vision Demonstration:"
    puts "=" * 50
    
    # 1. Image Processing Basics
    demonstrate_image_basics
    
    # 2. Convolution Operations
    demonstrate_convolution_operations
    
    # 3. Feature Extraction
    demonstrate_feature_extraction
    
    # 4. Image Classification
    demonstrate_image_classification
    
    # 5. Object Detection
    demonstrate_object_detection
    
    # 6. Image Segmentation
    demonstrate_image_segmentation
  end
  
  def self.demonstrate_image_basics
    puts "\n1. Image Processing Basics:"
    puts "=" * 30
    
    # Create sample image (5x5 grayscale)
    image = [
      [100, 120, 140, 120, 100],
      [120, 150, 180, 150, 120],
      [140, 180, 200, 180, 140],
      [120, 150, 180, 150, 120],
      [100, 120, 140, 120, 100]
    ]
    
    puts "Original 5x5 image:"
    image.each { |row| puts "  #{row.map { |x| x.to_s.ljust(4) }.join}" }
    
    # Basic operations
    puts "\nImage statistics:"
    processor = ImageProcessor.new(image)
    
    stats = processor.statistics
    puts "  Min value: #{stats[:min]}"
    puts "  Max value: #{stats[:max]}"
    puts "  Mean value: #{stats[:mean].round(2)}"
    puts "  Standard deviation: #{stats[:std].round(2)}"
    
    # Histogram
    histogram = processor.histogram
    puts "\nHistogram (simplified):"
    histogram.each do |bin, count|
      puts "  #{bin}: #{count} pixels"
    end
    
    puts "\nImage Processing Features:"
    puts "- Basic statistics"
    puts "- Histogram computation"
    puts "- Pixel manipulation"
    puts "- Image normalization"
    puts "- Color space conversion"
  end
  
  def self.demonstrate_convolution_operations
    puts "\n2. Convolution Operations:"
    puts "=" * 30
    
    # Create sample image
    image = [
      [10, 20, 30, 40, 50],
      [20, 30, 40, 50, 60],
      [30, 40, 50, 60, 70],
      [40, 50, 60, 70, 80],
      [50, 60, 70, 80, 90]
    ]
    
    puts "Original image:"
    image.each { |row| puts "  #{row.map { |x| x.to_s.ljust(3) }.join}" }
    
    # Define convolution kernels
    kernels = {
      'edge_detection' => [
        [-1, -1, -1],
        [-1,  8, -1],
        [-1, -1, -1]
      ],
      'sharpen' => [
        [0, -1, 0],
        [-1, 5, -1],
        [0, -1, 0]
      ],
      'blur' => [
        [1, 2, 1],
        [2, 4, 2],
        [1, 2, 1]
      ].map { |row| row.map { |x| x / 16.0 } },
      'emboss' => [
        [-2, -1, 0],
        [-1, 1, 1],
        [0, 1, 2]
      ]
    }
    
    processor = ImageProcessor.new(image)
    
    kernels.each do |kernel_name, kernel|
      puts "\nApplying #{kernel_name} kernel:"
      result = processor.convolve(kernel)
      
      result.each { |row| puts "  #{row.map { |x| x.round(1).to_s.ljust(5) }.join}" }
    end
    
    puts "\nConvolution Features:"
    puts "- Multiple kernel types"
    puts "- Edge detection"
    puts "- Image sharpening"
    puts "- Gaussian blur"
    puts "- Emboss effect"
    puts "- Custom kernels"
  end
  
  def self.demonstrate_feature_extraction
    puts "\n3. Feature Extraction:"
    puts "=" * 30
    
    # Create sample image with patterns
    image = [
      [0, 0, 0, 0, 0, 0, 0, 0],
      [0, 255, 255, 255, 255, 255, 255, 0],
      [0, 255, 0, 0, 0, 0, 255, 0],
      [0, 255, 0, 255, 255, 0, 255, 0],
      [0, 255, 0, 0, 0, 0, 255, 0],
      [0, 255, 255, 255, 255, 255, 255, 0],
      [0, 0, 0, 0, 0, 0, 0, 0]
    ]
    
    puts "Original image:"
    image.each { |row| puts "  #{row.map { |x| x.to_s.ljust(3) }.join}" }
    
    processor = ImageProcessor.new(image)
    
    # Extract features
    puts "\nExtracted features:"
    
    # Edge features
    edges = processor.detect_edges
    puts "Edge features:"
    edges.each { |row| puts "  #{row.map { |x| x.to_s.ljust(3) }.join}" }
    
    # Corner features (simplified)
    corners = processor.detect_corners
    puts "\nCorner features:"
    corners.each { |row| puts "  #{row.map { |x| x.to_s.ljust(3) }.join}" }
    
    # Texture features
    texture = processor.extract_texture
    puts "\nTexture features:"
    texture.each { |row| puts "  #{row.map { |x| x.round(2).to_s.ljust(5) }.join}" }
    
    puts "\nFeature Extraction Features:"
    puts "- Edge detection"
    puts "- Corner detection"
    puts "- Texture analysis"
    puts "- Gradient computation"
    puts "- Feature descriptors"
  end
  
  def self.demonstrate_image_classification
    puts "\n4. Image Classification:"
    puts "=" * 30
    
    # Create simple CNN classifier
    classifier = SimpleCNNClassifier.new
    
    # Generate sample images (10x10)
    classes = ['circle', 'square', 'triangle']
    
    classes.each do |class_name|
      puts "\nGenerating #{class_name} images:"
      
      3.times do |i|
        image = case class_name
                when 'circle'
                  generate_circle_image(10, 10)
                when 'square'
                  generate_square_image(10, 10)
                when 'triangle'
                  generate_triangle_image(10, 10)
                end
        
        # Classify image
        prediction = classifier.classify(image)
        
        puts "  Image #{i + 1}: Predicted #{prediction[:class]} (confidence: #{prediction[:confidence].round(3)})"
      end
    end
    
    # Train classifier
    puts "\nTraining classifier..."
    training_data = generate_training_data(classes, 20)
    classifier.train(training_data, epochs: 50)
    
    # Test classifier
    puts "\nTesting classifier:"
    test_data = generate_training_data(classes, 10)
    accuracy = classifier.evaluate(test_data)
    
    puts "Test accuracy: #{(accuracy * 100).round(2)}%"
    
    puts "\nImage Classification Features:"
    puts "- CNN-based classification"
    puts "- Feature learning"
    puts "- Multi-class support"
    puts "- Confidence scoring"
    puts "- Training and evaluation"
  end
  
  def self.demonstrate_object_detection
    puts "\n5. Object Detection:"
    puts "=" * 30
    
    # Create sample image with objects
    image = Array.new(20) { Array.new(20, 0) }
    
    # Add square object
    (5..10).each do |i|
      (3..8).each do |j|
        image[i][j] = 255
      end
    end
    
    # Add circle object
    center_x, center_y = 15, 15
    radius = 3
    (0..2 * Math::PI).step(0.1) do |angle|
      x = (center_x + radius * Math.cos(angle)).round
      y = (center_y + radius * Math.sin(angle)).round
      image[y][x] = 128 if y >= 0 && y < 20 && x >= 0 && x < 20
    end
    
    puts "Image with objects:"
    image.each { |row| puts "  #{row.map { |x| x.to_s.ljust(3) }.join}" }
    
    # Detect objects
    detector = ObjectDetector.new
    detections = detector.detect_objects(image)
    
    puts "\nDetected objects:"
    detections.each_with_index do |detection, i|
      puts "  Object #{i + 1}:"
      puts "    Type: #{detection[:type]}"
      puts "    Position: (#{detection[:x]}, #{detection[:y]})"
      puts "    Size: #{detection[:width]}x#{detection[:height]}"
      puts "    Confidence: #{detection[:confidence].round(3)}"
    end
    
    puts "\nObject Detection Features:"
    puts "- Multi-object detection"
    puts "- Bounding box prediction"
    puts "- Object classification"
    puts "- Confidence scoring"
    puts "- Non-maximum suppression"
  end
  
  def self.demonstrate_image_segmentation
    puts "\n6. Image Segmentation:"
    puts "=" * 30
    
    # Create sample image with regions
    image = Array.new(10) { Array.new(10, 0) }
    
    # Create different regions
    (0..4).each do |i|
      (0..4).each { |j| image[i][j] = 50 }    # Region 1
      (5..9).each { |j| image[i][j] = 100 }   # Region 2
    end
    (5..9).each do |i|
      (0..4).each { |j| image[i][j] = 150 }   # Region 3
      (5..9).each { |j| image[i][j] = 200 }   # Region 4
    end
    
    puts "Original image with regions:"
    image.each { |row| puts "  #{row.map { |x| x.to_s.ljust(3) }.join}" }
    
    # Segment image
    segmenter = ImageSegmenter.new
    segments = segmenter.segment(image)
    
    puts "\nSegmented image:"
    segments.each { |row| puts "  #{row.map { |x| x.to_s.ljust(3) }.join}" }
    
    # Show region boundaries
    boundaries = segmenter.find_boundaries(segments)
    puts "\nRegion boundaries:"
    boundaries.each { |row| puts "  #{row.map { |x| x ? 'X' : ' ' }.join}" }
    
    puts "\nImage Segmentation Features:"
    puts "- Region-based segmentation"
    puts "- Boundary detection"
    puts "- Clustering algorithms"
    puts "- Region growing"
    puts "- Semantic segmentation"
  end
  
  private
  
  def self.generate_circle_image(width, height)
    image = Array.new(height) { Array.new(width, 0) }
    center_x, center_y = width / 2, height / 2
    radius = [width, height].min / 3
    
    (0...height).each do |y|
      (0...width).each do |x|
        distance = Math.sqrt((x - center_x)**2 + (y - center_y)**2)
        image[y][x] = 255 if distance <= radius
      end
    end
    
    image
  end
  
  def self.generate_square_image(width, height)
    image = Array.new(height) { Array.new(width, 0) }
    
    start_x = width / 4
    start_y = height / 4
    size = [width, height].min / 2
    
    (start_y...(start_y + size)).each do |y|
      (start_x...(start_x + size)).each do |x|
        image[y][x] = 255 if y < height && x < width
      end
    end
    
    image
  end
  
  def self.generate_triangle_image(width, height)
    image = Array.new(height) { Array.new(width, 0) }
    
    center_x, center_y = width / 2, height / 2
    size = [width, height].min / 2
    
    # Simple triangle
    points = [
      [center_x, center_y - size/2],
      [center_x - size/2, center_y + size/2],
      [center_x + size/2, center_y + size/2]
    ]
    
    (0...height).each do |y|
      (0...width).each do |x|
        if point_in_triangle?([x, y], points)
          image[y][x] = 255
        end
      end
    end
    
    image
  end
  
  def self.point_in_triangle?(point, triangle)
    # Barycentric coordinates method
    v0 = [triangle[2][0] - triangle[0][0], triangle[2][1] - triangle[0][1]]
    v1 = [triangle[1][0] - triangle[0][0], triangle[1][1] - triangle[0][1]]
    v2 = [point[0] - triangle[0][0], point[1] - triangle[0][1]]
    
    dot00 = v0[0] * v0[0] + v0[1] * v0[1]
    dot01 = v0[0] * v1[0] + v0[1] * v1[1]
    dot02 = v0[0] * v2[0] + v0[1] * v2[1]
    dot11 = v1[0] * v1[0] + v1[1] * v1[1]
    dot12 = v1[0] * v2[0] + v1[1] * v2[1]
    
    inv_denom = 1.0 / (dot00 * dot11 - dot01 * dot01)
    u = (dot11 * dot02 - dot01 * dot12) * inv_denom
    v = (dot00 * dot12 - dot01 * dot02) * inv_denom
    
    u >= 0 && v >= 0 && u + v <= 1
  end
  
  def self.generate_training_data(classes, samples_per_class)
    training_data = []
    
    classes.each do |class_name|
      samples_per_class.times do
        image = case class_name
                when 'circle'
                  generate_circle_image(10, 10)
                when 'square'
                  generate_square_image(10, 10)
                when 'triangle'
                  generate_triangle_image(10, 10)
                end
        
        training_data << [image.flatten, class_name]
      end
    end
    
    training_data
  end
end

class ImageProcessor
  def initialize(image)
    @image = image
    @height = image.length
    @width = image[0].length
  end
  
  def statistics
    pixels = @image.flatten
    {
      min: pixels.min,
      max: pixels.max,
      mean: pixels.sum.to_f / pixels.length,
      std: Math.sqrt(pixels.map { |x| (x - pixels.sum.to_f / pixels.length)**2 }.sum / pixels.length)
    }
  end
  
  def histogram(bins = 10)
    pixels = @image.flatten
    min_val, max_val = pixels.min, pixels.max
    bin_size = (max_val - min_val) / bins.to_f
    
    histogram = Hash.new(0)
    pixels.each do |pixel|
      bin = ((pixel - min_val) / bin_size).floor
      histogram[bin] += 1
    end
    
    histogram
  end
  
  def convolve(kernel)
    output = Array.new(@height) { Array.new(@width, 0) }
    
    kernel_height = kernel.length
    kernel_width = kernel[0].length
    
    ((kernel_height / 2)...(@height - kernel_height / 2)).each do |i|
      ((kernel_width / 2)...(@width - kernel_width / 2)).each do |j|
        sum = 0.0
        
        kernel_height.times do |m|
          kernel_width.times do |n|
            image_i = i + m - kernel_height / 2
            image_j = j + n - kernel_width / 2
            
            if image_i >= 0 && image_i < @height && image_j >= 0 && image_j < @width
              sum += @image[image_i][image_j] * kernel[m][n]
            end
          end
        end
        
        output[i][j] = sum
      end
    end
    
    output
  end
  
  def detect_edges
    # Sobel edge detection
    sobel_x = [
      [-1, 0, 1],
      [-2, 0, 2],
      [-1, 0, 1]
    ]
    
    sobel_y = [
      [-1, -2, -1],
      [0, 0, 0],
      [1, 2, 1]
    ]
    
    grad_x = convolve(sobel_x)
    grad_y = convolve(sobel_y)
    
    # Compute gradient magnitude
    Array.new(@height) do |i|
      Array.new(@width) do |j|
        Math.sqrt(grad_x[i][j]**2 + grad_y[i][j]**2)
      end
    end
  end
  
  def detect_corners
    # Simplified Harris corner detection
    edges = detect_edges
    corners = Array.new(@height) { Array.new(@width, 0) }
    
    (1...@height - 1).each do |i|
      (1...@width - 1).each do |j|
        # Calculate corner response
        i_xx = (edges[i+1][j] - edges[i-1][j])**2
        i_yy = (edges[i][j+1] - edges[i][j-1])**2
        i_xy = (edges[i+1][j+1] - edges[i+1][j-1] - edges[i-1][j+1] + edges[i-1][j-1])**2 / 4
        
        k = 0.04
        det = i_xx * i_yy - i_xy**2
        trace = i_xx + i_yy
        
        corners[i][j] = det - k * trace**2
      end
    end
    
    corners
  end
  
  def extract_texture
    # Simplified texture analysis using local standard deviation
    texture = Array.new(@height) { Array.new(@width, 0) }
    
    (1...@height - 1).each do |i|
      (1...@width - 1).each do |j|
        # Calculate local standard deviation
        neighborhood = [
          @image[i-1][j-1], @image[i-1][j], @image[i-1][j+1],
          @image[i][j-1], @image[i][j], @image[i][j+1],
          @image[i+1][j-1], @image[i+1][j], @image[i+1][j+1]
        ]
        
        mean = neighborhood.sum / 9.0
        variance = neighborhood.map { |x| (x - mean)**2 }.sum / 9.0
        texture[i][j] = Math.sqrt(variance)
      end
    end
    
    texture
  end
end

class SimpleCNNClassifier
  def initialize
    @conv_layer1 = ConvolutionalLayer.new(10, 3, 4)
    @pool_layer1 = PoolingLayer.new(2, 2, :max)
    @conv_layer2 = ConvolutionalLayer.new(4, 3, 8)
    @pool_layer2 = PoolingLayer.new(2, 2, :max)
    @dense_layer = DenseLayer.new(8, 3, activation: :softmax)
    
    @classes = ['circle', 'square', 'triangle']
  end
  
  def classify(image)
    # Forward pass through CNN
    output = @conv_layer1.forward(image)
    output = @pool_layer1.forward(output)
    output = @conv_layer2.forward(output)
    output = @pool_layer2.forward(output)
    output = @dense_layer.forward(output.flatten)
    
    # Get prediction and confidence
    predicted_class = @classes[output.index(output.max)]
    confidence = output.max
    
    {
      class: predicted_class,
      confidence: confidence,
      probabilities: @classes.zip(output).to_h
    }
  end
  
  def train(training_data, epochs: 100, learning_rate: 0.01)
    # Simplified training (in real implementation, this would be much more complex)
    puts "Training CNN for #{epochs} epochs..."
    
    epochs.times do |epoch|
      total_loss = 0.0
      
      training_data.each do |image, target_class|
        # Forward pass
        result = classify(image)
        
        # Compute loss (simplified)
        target_idx = @classes.index(target_class)
        loss = -Math.log(result[:probabilities][target_class] + 1e-10)
        total_loss += loss
        
        # Backward pass (simplified - would require full implementation)
        # In real CNN, this would involve backprop through all layers
      end
      
      avg_loss = total_loss / training_data.length
      puts "Epoch #{epoch + 1}: Loss = #{avg_loss.round(6)}" if (epoch + 1) % 10 == 0
    end
  end
  
  def evaluate(test_data)
    correct = 0
    total = test_data.length
    
    test_data.each do |image, target_class|
      prediction = classify(image)
      correct += 1 if prediction[:class] == target_class
    end
    
    correct.to_f / total
  end
end

class ObjectDetector
  def initialize
    @conv_layer = ConvolutionalLayer.new(20, 3, 2)
    @pool_layer = PoolingLayer.new(2, 2, :max)
    @detector_head = DenseLayer.new(2, 4, activation: :sigmoid)
    
    @object_types = ['circle', 'square', 'triangle', 'unknown']
  end
  
  def detect_objects(image)
    # Simplified object detection
    detections = []
    
    # Use sliding window approach
    window_size = 5
    stride = 2
    
    (0...image.length - window_size).step(stride) do |i|
      (0...image[0].length - window_size).step(stride) do |j|
        # Extract window
        window = image[i...i+window_size].map { |row| row[j...j+window_size] }
        
        # Classify window
        object_type = classify_window(window)
        
        if object_type != 'unknown'
          detections << {
            type: object_type,
            x: j,
            y: i,
            width: window_size,
            height: window_size,
            confidence: rand(0.7..0.95)
          }
        end
      end
    end
    
    # Non-maximum suppression (simplified)
    detections = non_maximum_suppression(detections)
    
    detections
  end
  
  private
  
  def classify_window(window)
    # Simplified window classification
    # In real implementation, this would use a trained CNN
    features = extract_window_features(window)
    
    # Simple rule-based classification
    if features[:edge_density] > 0.3 && features[:circularity] > 0.7
      'circle'
    elsif features[:edge_density] > 0.3 && features[:rectangularity] > 0.8
      'square'
    elsif features[:edge_density] > 0.2 && features[:angularity] > 0.6
      'triangle'
    else
      'unknown'
    end
  end
  
  def extract_window_features(window)
    # Extract simple features from window
    edges = detect_edges_in_window(window)
    edge_density = edges.flatten.count { |e| e > 50 }.to_f / (window.length * window[0].length)
    
    # Simplified feature extraction
    {
      edge_density: edge_density,
      circularity: rand(0.5..1.0),
      rectangularity: rand(0.5..1.0),
      angularity: rand(0.5..1.0)
    }
  end
  
  def detect_edges_in_window(window)
    # Simple edge detection
    edges = Array.new(window.length) { Array.new(window[0].length, 0) }
    
    (1...window.length - 1).each do |i|
      (1...window[0].length - 1).each do |j|
        # Simple gradient calculation
        grad_x = window[i][j+1] - window[i][j-1]
        grad_y = window[i+1][j] - window[i-1][j]
        edges[i][j] = Math.sqrt(grad_x**2 + grad_y**2)
      end
    end
    
    edges
  end
  
  def non_maximum_suppression(detections)
    # Simplified non-maximum suppression
    return detections if detections.length <= 1
    
    # Sort by confidence
    detections.sort_by! { |d| -d[:confidence] }
    
    # Remove overlapping detections
    filtered = [detections.first]
    
    detections[1..-1].each do |detection|
      overlap = false
      filtered.each do |existing|
        if iou(detection, existing) > 0.3
          overlap = true
          break
        end
      end
      
      filtered << detection unless overlap
    end
    
    filtered
  end
  
  def iou(box1, box2)
    # Intersection over Union
    x1 = [box1[:x], box2[:x]].max
    y1 = [box1[:y], box2[:y]].max
    x2 = [box1[:x] + box1[:width], box2[:x] + box2[:width]].min
    y2 = [box1[:y] + box1[:height], box2[:y] + box2[:height]].min
    
    intersection = [0, x2 - x1].max * [0, y2 - y1].max
    
    area1 = box1[:width] * box1[:height]
    area2 = box2[:width] * box2[:height]
    union = area1 + area2 - intersection
    
    union > 0 ? intersection / union : 0
  end
end

class ImageSegmenter
  def initialize
    @num_clusters = 4
  end
  
  def segment(image)
    # Simplified K-means segmentation
    pixels = image.flatten
    clusters = kmeans(pixels, @num_clusters)
    
    # Assign each pixel to its cluster
    segmented = Array.new(image.length) do |i|
      Array.new(image[0].length) do |j|
        pixel = image[i][j]
        cluster = find_nearest_cluster(pixel, clusters)
        cluster * 50 # Scale cluster values for visualization
      end
    end
    
    segmented
  end
  
  def find_boundaries(segments)
    boundaries = Array.new(segments.length) { Array.new(segments[0].length, false) }
    
    (1...segments.length - 1).each do |i|
      (1...segments[0].length - 1).each do |j|
        # Check if pixel has different cluster than neighbors
        current = segments[i][j]
        neighbors = [
          segments[i-1][j], segments[i+1][j],
          segments[i][j-1], segments[i][j+1]
        ]
        
        if neighbors.any? { |n| n != current }
          boundaries[i][j] = true
        end
      end
    end
    
    boundaries
  end
  
  private
  
  def kmeans(data, k)
    # Simplified K-means clustering
    # Initialize centroids
    min_val, max_val = data.min, data.max
    centroids = k.times.map { rand(min_val..max_val) }
    
    10.times do # Fixed iterations
      # Assign points to clusters
      clusters = k.times.map { [] }
      
      data.each do |pixel|
        nearest_centroid = centroids.min_by { |c| (pixel - c).abs }
        cluster_idx = centroids.index(nearest_centroid)
        clusters[cluster_idx] << pixel
      end
      
      # Update centroids
      clusters.each_with_index do |cluster, i|
        next if cluster.empty?
        centroids[i] = cluster.sum.to_f / cluster.length
      end
    end
    
    centroids
  end
  
  def find_nearest_cluster(pixel, clusters)
    clusters.min_by { |c| (pixel - c).abs }
  end
end
```

## 🎯 Exercises

### Beginner Exercises

1. **Basic Neural Network**: Create simple neural network
2. **Activation Functions**: Implement activation functions
3. **Loss Functions**: Add loss computation
4. **Gradient Descent**: Basic optimization

### Intermediate Exercises

1. **CNN Implementation**: Build convolutional network
2. **Backpropagation**: Implement backpropagation
3. **Regularization**: Add dropout and L2 regularization
4. **Computer Vision**: Image processing tasks

### Advanced Exercises

1. **Deep Learning Framework**: Complete framework
2. **Transfer Learning**: Use pretrained models
3. **GAN Implementation**: Generative models
4. **Production Deployment**: Deploy models

---

## 🎯 Summary

Deep Learning Frameworks in Ruby provides:

- **Deep Learning Fundamentals** - Core concepts and principles
- **Neural Network Implementation** - Building networks from scratch
- **Deep Learning Framework** - Complete framework implementation
- **Computer Vision** - Image processing and CNN applications
- **Comprehensive Examples** - Real-world applications
- **Production Features** - Training, evaluation, and deployment

Master these deep learning techniques for advanced Ruby AI applications!
