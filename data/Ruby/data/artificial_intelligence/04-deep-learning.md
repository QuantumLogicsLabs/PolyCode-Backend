# Deep Learning in Ruby
# Comprehensive guide to deep learning architectures and advanced techniques

## 🧠 Deep Learning Fundamentals

### 1. Deep Learning Concepts

Advanced concepts and architectures:

```ruby
class DeepLearningBasics
  def self.explain_deep_concepts
    puts "Deep Learning Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Deep Neural Networks",
        description: "Neural networks with multiple hidden layers",
        characteristics: ["Hierarchical feature learning", "Automatic feature extraction", "End-to-end learning"],
        advantages: ["Better representation", "Reduced feature engineering", "State-of-the-art performance"]
      },
      {
        concept: "Feature Hierarchy",
        description: "Layers learn increasingly abstract features",
        example: "Edges → Shapes → Objects → Scenes",
        importance: "Enables complex pattern recognition"
      },
      {
        concept: "Vanishing/Exploding Gradients",
        description: "Gradient problems in deep networks",
        causes: ["Deep architectures", "Activation functions", "Weight initialization"],
        solutions: ["ReLU activation", "Batch normalization", "Residual connections"]
      },
      {
        concept: "Transfer Learning",
        description: "Use pre-trained models for new tasks",
        benefits: ["Faster training", "Better performance", "Less data required"],
        approaches: ["Feature extraction", "Fine-tuning", "Domain adaptation"]
      },
      {
        concept: "Attention Mechanisms",
        description: "Focus on relevant parts of input",
        applications: ["Machine translation", "Image captioning", "Speech recognition"],
        types: ["Self-attention", "Cross-attention", "Multi-head attention"]
      },
      {
        concept: "Autoencoders",
        description: "Neural networks for unsupervised learning",
        types: ["Denoising", "Variational", "Sparse"],
        uses: ["Dimensionality reduction", "Feature learning", "Anomaly detection"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Characteristics: #{concept[:characteristics].join(', ')}" if concept[:characteristics]
      puts "  Advantages: #{concept[:advantages].join(', ')}" if concept[:advantages]
      puts "  Example: #{concept[:example]}" if concept[:example]
      puts "  Importance: #{concept[:importance]}" if concept[:importance]
      puts "  Causes: #{concept[:causes].join(', ')}" if concept[:causes]
      puts "  Solutions: #{concept[:solutions].join(', ')}" if concept[:solutions]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Approaches: #{concept[:approaches].join(', ')}" if concept[:approaches]
      puts "  Applications: #{concept[:applications].join(', ')}" if concept[:applications]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Uses: #{concept[:uses].join(', ')}" if concept[:uses]
      puts
    end
  end
  
  def self.deep_architectures
    puts "\nDeep Learning Architectures:"
    puts "=" * 50
    
    architectures = [
      {
        name: "Convolutional Neural Networks (CNN)",
        primary_use: "Image processing",
        key_components: ["Convolutional layers", "Pooling layers", "Fully connected layers"],
        applications: ["Image classification", "Object detection", "Segmentation"],
        famous_models: ["LeNet", "AlexNet", "VGG", "ResNet", "Inception"]
      },
      {
        name: "Recurrent Neural Networks (RNN)",
        primary_use: "Sequential data",
        key_components: ["Recurrent connections", "Hidden states", "Temporal dynamics"],
        applications: ["Language modeling", "Time series", "Speech recognition"],
        famous_models: ["LSTM", "GRU", "Bidirectional RNN"]
      },
      {
        name: "Transformer Networks",
        primary_use: "Sequence processing",
        key_components: ["Self-attention", "Multi-head attention", "Positional encoding"],
        applications: ["Machine translation", "Text generation", "Vision transformers"],
        famous_models: ["BERT", "GPT", "T5", "ViT"]
      },
      {
        name: "Generative Adversarial Networks (GAN)",
        primary_use: "Data generation",
        key_components: ["Generator", "Discriminator", "Adversarial training"],
        applications: ["Image generation", "Style transfer", "Data augmentation"],
        famous_models: ["DCGAN", "StyleGAN", "CycleGAN", "Pix2Pix"]
      },
      {
        name: "Autoencoder Variants",
        primary_use: "Representation learning",
        key_components: ["Encoder", "Decoder", "Bottleneck"],
        applications: ["Dimensionality reduction", "Anomaly detection", "Denoising"],
        famous_models: ["VAE", "DAE", "SAE", "Contractive Autoencoder"]
      }
    ]
    
    architectures.each do |arch|
      puts "#{arch[:name]}:"
      puts "  Primary Use: #{arch[:primary_use]}"
      puts "  Key Components: #{arch[:key_components].join(', ')}"
      puts "  Applications: #{arch[:applications].join(', ')}"
      puts "  Famous Models: #{arch[:famous_models].join(', ')}"
      puts
    end
  end
  
  # Run deep learning basics
  explain_deep_concepts
  deep_architectures
end
```

### 2. Residual Networks

Deep networks with skip connections:

```ruby
class ResidualBlock
  def initialize(input_channels, output_channels, stride = 1)
    @input_channels = input_channels
    @output_channels = output_channels
    @stride = stride
    
    # Main path layers
    @conv1 = ConvolutionalLayer.new(input_channels, output_channels, 3, stride, 1)
    @bn1 = BatchNormalizationLayer.new(output_channels)
    @conv2 = ConvolutionalLayer.new(output_channels, output_channels, 3, 1, 1)
    @bn2 = BatchNormalizationLayer.new(output_channels)
    
    # Skip connection
    if stride != 1 || input_channels != output_channels
      @shortcut_conv = ConvolutionalLayer.new(input_channels, output_channels, 1, stride, 0)
      @shortcut_bn = BatchNormalizationLayer.new(output_channels)
    else
      @shortcut_conv = nil
      @shortcut_bn = nil
    end
  end
  
  def forward(input)
    # Store input for skip connection
    @input = input
    
    # Main path
    x = @conv1.forward(input)
    x = @bn1.forward(x)
    x = x.map { |channel| channel.map { |row| row.map { |val| ActivationFunctions.relu(val) } } }
    
    x = @conv2.forward(x)
    x = @bn2.forward(x)
    
    # Skip connection
    if @shortcut_conv
      shortcut = @shortcut_conv.forward(@input)
      shortcut = @shortcut_bn.forward(shortcut)
    else
      shortcut = @input
    end
    
    # Add and activate
    @output = x.zip(shortcut).map do |main_ch, skip_ch|
      main_ch.zip(skip_ch).map { |main_row, skip_row| main_row.zip(skip_row).map { |main_val, skip_val| main_val + skip_val } }
    end
    
    @output.map { |channel| channel.map { |row| row.map { |val| ActivationFunctions.relu(val) } } }
  end
  
  def backward(output_gradient, learning_rate)
    # Gradient through ReLU
    relu_gradient = @output.zip(@input).map do |out_ch, in_ch|
      out_ch.zip(in_ch).map do |out_row, in_row|
        out_row.zip(in_row).map { |out_val, in_val| out_val > 0 ? 1 : 0 }
      end
    end
    
    # Gradient through addition
    main_gradient = output_gradient.zip(relu_gradient).map do |out_grad, relu_grad|
      out_grad.zip(relu_grad).map do |out_row, relu_row| out_row.zip(relu_row).map { |out_val, relu_val| out_val * relu_val } }
    end
    
    # Backward through main path
    conv2_gradient = @bn2.backward(main_gradient, learning_rate)
    conv1_gradient = @conv1.backward(conv2_gradient, learning_rate)
    
    # Backward through skip connection
    if @shortcut_conv
      shortcut_gradient = @shortcut_bn.backward(main_gradient, learning_rate)
      @shortcut_conv.backward(shortcut_gradient, learning_rate)
    end
    
    conv1_gradient
  end
end

class ResNet
  def initialize(block_config, num_classes = 1000)
    @block_config = block_config  # e.g., [3, 4, 6, 3] for ResNet-50
    @num_classes = num_classes
    
    # Build network
    @layers = []
    build_network
  end
  
  def forward(input)
    current_input = input
    
    @layers.each do |layer|
      current_input = layer.forward(current_input)
    end
    
    current_input
  end
  
  def train_step(input, target, learning_rate = 0.001)
    # Forward pass
    output = forward(input)
    
    # Calculate loss gradient (simplified)
    loss_gradient = calculate_loss_gradient(output, target)
    
    # Backward pass
    gradient = loss_gradient
    
    @layers.reverse_each do |layer|
      gradient = layer.backward(gradient, learning_rate)
    end
    
    # Calculate loss
    loss = calculate_loss(output, target)
    loss
  end
  
  private
  
  def build_network
    # Initial convolution
    @layers << ConvolutionalLayer.new(3, 64, 7, 2, 3)
    @layers << BatchNormalizationLayer.new(64)
    
    # Residual blocks
    channels = 64
    @block_config.each_with_index do |num_blocks, stage_idx|
      if stage_idx > 0
        # Downsample at stage start
        @layers << ResidualBlock.new(channels, channels * 2, 2)
        channels *= 2
      end
      
      num_blocks.times do
        @layers << ResidualBlock.new(channels, channels, 1)
      end
    end
    
    # Global average pooling (simplified)
    @layers << GlobalAveragePoolingLayer.new
    
    # Final classification layer
    @layers << FullyConnectedLayer.new(channels, @num_classes)
  end
  
  def calculate_loss(output, target)
    # Cross-entropy loss
    output[target] = output[target] - 1
    -Math.log(output[target] + 1e-15)
  end
  
  def calculate_loss_gradient(output, target)
    gradient = output.dup
    gradient[target] -= 1
    gradient
  end
end

class BatchNormalizationLayer
  def initialize(num_features, momentum = 0.9, epsilon = 1e-5)
    @num_features = num_features
    @momentum = momentum
    @epsilon = epsilon
    
    # Learnable parameters
    @gamma = Array.new(num_features, 1.0)
    @beta = Array.new(num_features, 0.0)
    
    # Running statistics
    @running_mean = Array.new(num_features, 0.0)
    @running_var = Array.new(num_features, 1.0)
    
    @input = nil
    @normalized = nil
  end
  
  def forward(input, training = true)
    @input = input
    
    # Calculate mean and variance
    mean = calculate_mean(input)
    variance = calculate_variance(input, mean)
    
    if training
      # Update running statistics
      @running_mean = @momentum * @running_mean + (1 - @momentum) * mean
      @running_var = @momentum * @running_var + (1 - @momentum) * variance
    else
      # Use running statistics
      mean = @running_mean
      variance = @running_var
    end
    
    # Normalize
    @normalized = input.map.with_index do |channel, c|
      channel.map do |row|
        row.map { |val| (val - mean[c]) / Math.sqrt(variance[c] + @epsilon) }
      end
    end
    
    # Scale and shift
    @output = @normalized.map.with_index do |channel, c|
      channel.map do |row|
        row.map { |val| @gamma[c] * val + @beta[c] }
      end
    end
  end
  
  def backward(output_gradient, learning_rate)
    # Simplified backward pass
    input_gradient = @normalized.map.with_index do |channel, c|
      channel.map do |row|
        row.map { |val| val * @gamma[c] }
      end
    end
    
    # Update parameters
    @gamma.each_with_index do |gamma, i|
      gradient = output_gradient[i].sum
      @gamma[i] -= learning_rate * gradient
    end
    
    @beta.each_with_index do |beta, i|
      gradient = output_gradient[i].sum
      @beta[i] -= learning_rate * gradient
    end
    
    input_gradient
  end
  
  private
  
  def calculate_mean(input)
    input.map do |channel|
      channel.sum / channel.flatten.length
    end
  end
  
  def calculate_variance(input, mean)
    input.map.with_index do |channel, c|
      channel.flatten.sum { |val| (val - mean[c]) ** 2 } / channel.flatten.length
    end
  end
end

class GlobalAveragePoolingLayer
  def forward(input)
    @input_shape = input.shape
    channels, height, width = input.length, input[0].length, input[0][0].length
    
    @output = channels.times.map do |c|
      input[c].flatten.sum / (height * width)
    end
  end
  
  def backward(output_gradient, learning_rate = nil)
    # Distribute gradient evenly
    channels, height, width = @input_shape
    output_gradient.map do |grad|
      grad / (height * width)
    end
  end
end

class FullyConnectedLayer
  def initialize(input_size, output_size)
    @input_size = input_size
    @output_size = output_size
    
    # Initialize weights
    @weights = Array.new(output_size) { Array.new(input_size) { rand * 0.1 - 0.05 } }
    @biases = Array.new(output_size, 0)
  end
  
  def forward(input)
    @input = input
    @output = @weights.map.with_index do |weights_row, i|
      bias = @biases[i]
      input.zip(weights_row).sum { |x, w| x * w } + bias
    end
  end
  
  def backward(output_gradient, learning_rate)
    # Update weights
    @weights.each_with_index do |weights_row, i|
      weights_row.each_with_index do |weight, j|
        gradient = output_gradient[i] * @input[j]
        weights_row[j] = weight - learning_rate * gradient
      end
      @biases[i] -= learning_rate * output_gradient[i]
    end
    
    # Input gradient
    @input.map.with_index do |input_val, j|
      @weights.zip(output_gradient).sum { |weights_row, grad| weights_row[j] * grad }
    end
  end
end

def self.demonstrate_resnet
  puts "Residual Network Demonstration:"
  puts "=" * 50
  
  # Create ResNet-18 style network
  block_config = [2, 2, 2, 2]  # Simplified ResNet-18
  resnet = ResNet.new(block_config, 10)
  
  # Generate sample data (simplified)
  puts "Generating sample image data..."
  
  # Create sample 64x64x3 images
  X = 20.times.map do
    Array.new(3) do
      Array.new(64) do
        Array.new(64) { rand }
      end
    end
  end
  
  # Generate labels
  y = 20.times.map { rand(10) }
  
  puts "Generated #{X.length} sample images"
  puts "Image shape: #{X[0][0].length}x#{X[0][0][0].length}x#{X[0].length}"
  
  # Train for a few epochs
  puts "\nTraining ResNet..."
  
  5.times do |epoch|
    total_loss = 0
    
    X.each_with_index do |image, i|
      loss = resnet.train_step(image, y[i], 0.001)
      total_loss += loss
    end
    
    avg_loss = total_loss / X.length
    puts "Epoch #{epoch + 1}: Loss = #{avg_loss.round(4)}"
  end
  
  puts "\nResNet training completed!"
  puts "Key features demonstrated:"
  puts "- Residual blocks with skip connections"
  puts "- Batch normalization"
  puts "- Global average pooling"
  puts "- Deep network architecture"
end
```

### 3. Recurrent Neural Networks

Memory-based networks for sequences:

```ruby
class LSTMCell
  def initialize(input_size, hidden_size)
    @input_size = input_size
    @hidden_size = hidden_size
    
    # Initialize weights and biases
    @w_f = Array.new(hidden_size) { Array.new(input_size + hidden_size, 0) }  # Forget gate
    @w_i = Array.new(hidden_size) { Array.new(input_size + hidden_size, 0) }  # Input gate
    @w_o = Array.new(hidden_size) { Array.new(input_size + hidden_size, 0) }  # Output gate
    @w_c = Array.new(hidden_size) { Array.new(input_size + hidden_size, 0) }  # Candidate
    
    @b_f = Array.new(hidden_size, 0)
    @b_i = Array.new(hidden_size, 0)
    @b_o = Array.new(hidden_size, 0)
    @b_c = Array.new(hidden_size, 0)
  end
  
  def forward(input, hidden_state, cell_state)
    @input = input
    @prev_hidden = hidden_state
    @prev_cell = cell_state
    
    # Concatenate input and hidden state
    combined = input + hidden_state
    
    # Forget gate
    forget_gate = combined.map { |x| ActivationFunctions.sigmoid(x) }
    
    # Input gate
    input_gate = combined.map { |x| ActivationFunctions.sigmoid(x) }
    
    # Output gate
    output_gate = combined.map { |x| ActivationFunctions.sigmoid(x) }
    
    # Candidate values
    candidate = combined.map { |x| Math.tanh(x) }
    
    # Update cell state
    @cell_state = forget_gate.zip(@prev_cell, input_gate, candidate).map do |f, c, i, cand|
      f * c + i * cand
    end
    
    # Update hidden state
    @hidden_state = @cell_state.zip(output_gate).map do |c, o|
      Math.tanh(c) * o
    end
    
    [@hidden_state, @cell_state]
  end
  
  def backward(output_gradient, learning_rate)
    # Simplified backward pass
    # In practice, this would be much more complex
    input_gradient = Array.new(@input_size, 0)
    hidden_gradient = Array.new(@hidden_size, 0)
    
    # Update weights (simplified)
    [@w_f, @w_i, @w_o, @w_c].each do |weights|
      weights.each_with_index do |weight_row, i|
        weight_row.each_with_index do |weight, j|
          gradient = output_gradient[i] * (@input[j] + @prev_hidden[j])
          weight_row[j] -= learning_rate * gradient
        end
      end
    end
    
    [input_gradient, hidden_gradient]
  end
end

class SimpleRNN
  def initialize(input_size, hidden_size, output_size)
    @input_size = input_size
    @hidden_size = hidden_size
    @output_size = output_size
    
    # Initialize weights
    @w_xh = Array.new(hidden_size) { Array.new(input_size) { rand * 0.1 - 0.05 } }
    @w_hh = Array.new(hidden_size) { Array.new(hidden_size) { rand * 0.1 - 0.05 } }
    @w_hy = Array.new(output_size) { Array.new(hidden_size) { rand * 0.1 - 0.05 } }
    
    @b_h = Array.new(hidden_size, 0)
    @b_y = Array.new(output_size, 0)
  end
  
  def forward_sequence(sequence)
    hidden_state = Array.new(@hidden_size, 0)
    outputs = []
    
    sequence.each do |input|
      hidden_state = step_forward(input, hidden_state)
      output = step_output(hidden_state)
      outputs << output
    end
    
    outputs
  end
  
  def step_forward(input, hidden_state)
    # Update hidden state
    new_hidden = Array.new(@hidden_size) do |i|
      xh = input.zip(@w_xh[i]).sum { |x, w| x * w }
      hh = hidden_state.zip(@w_hh[i]).sum { |h, w| h * w }
      ActivationFunctions.tanh(xh + hh + @b_h[i])
    end
    
    new_hidden
  end
  
  def step_output(hidden_state)
    # Generate output
    Array.new(@output_size) do |i|
      hy = hidden_state.zip(@w_hy[i]).sum { |h, w| h * w }
      ActivationFunctions.sigmoid(hy + @b_y[i])
    end
  end
  
  def train_sequence(sequence, targets, learning_rate = 0.01)
    hidden_state = Array.new(@hidden_size, 0)
    total_loss = 0
    
    sequence.each_with_index do |input, t|
      # Forward pass
      hidden_state = step_forward(input, hidden_state)
      output = step_output(hidden_state)
      
      # Calculate loss
      target = targets[t]
      loss = -target * Math.log(output[0] + 1e-15) - (1 - target) * Math.log(1 - output[0] + 1e-15)
      total_loss += loss
      
      # Backward pass (simplified)
      output_gradient = output.dup
      output_gradient[0] -= target
      
      # Update output weights
      @w_hy.each_with_index do |weights_row, i|
        weights_row.each_with_index do |weight, j|
          gradient = output_gradient[i] * hidden_state[j]
          weights_row[j] -= learning_rate * gradient
        end
        @b_y[i] -= learning_rate * output_gradient[i]
      end
      
      # Hidden state gradient (simplified)
      hidden_gradient = @w_hy.transpose.map { |col| col.zip(output_gradient).sum { |w, g| w * g } }
      
      # Update recurrent weights (simplified)
      @w_hh.each_with_index do |weights_row, i|
        weights_row.each_with_index do |weight, j|
          gradient = hidden_gradient[i] * hidden_state[j]
          weights_row[j] -= learning_rate * gradient
        end
      end
    end
    
    total_loss / sequence.length
  end
  
  def self.demonstrate_rnn
    puts "Recurrent Neural Network Demonstration:"
    puts "=" * 50
    
    # Create simple RNN for sequence prediction
    rnn = SimpleRNN.new(3, 8, 2)
    
    # Generate sample sequence data
    puts "Generating sample sequence data..."
    
    # Generate sequences (simplified time series)
    sequences = []
    targets = []
    
    10.times do |i|
      sequence = 5.times.map do |j|
        [Math.sin(i + j * 0.1), Math.cos(i + j * 0.1), rand]
      end
      
      # Target: next value in sequence
      target = Math.sin(i + 5 * 0.1) > 0 ? 1 : 0
      
      sequences << sequence
      targets << target
    end
    
    puts "Generated #{sequences.length} sequences"
    puts "Sequence length: #{sequences[0].length}"
    puts "Target classes: #{targets.uniq}"
    
    # Train RNN
    puts "\nTraining RNN..."
    
    20.times do |epoch|
      total_loss = 0
      
      sequences.each_with_index do |sequence, i|
        loss = rnn.train_sequence(sequence, [targets[i]], 0.01)
        total_loss += loss
      end
      
      avg_loss = total_loss / sequences.length
      puts "Epoch #{epoch + 1}: Loss = #{avg_loss.round(4)}"
    end
    
    # Test predictions
    puts "\nSample predictions:"
    5.times do |i|
      sequence = sequences[i]
      outputs = rnn.forward_sequence(sequence)
      
      predicted_class = outputs.last[0] >= 0.5 ? 1 : 0
      actual_class = targets[i]
      
      puts "Sequence #{i}: Predicted #{predicted_class}, Actual #{actual_class}"
    end
    
    puts "\nRNN training completed!"
    puts "Key features demonstrated:"
    puts "- Recurrent connections"
    puts "- Sequence processing"
    puts "- Hidden state memory"
    puts "- Time series prediction"
  end
end
```

## 🤖 Advanced Deep Learning

### 4. Attention Mechanisms

Self-attention implementation:

```ruby
class AttentionMechanism
  def self.demonstrate_attention
    puts "Attention Mechanism Demonstration:"
    puts "=" * 50
    
    # Self-attention example
    puts "Self-Attention Mechanism:"
    puts "-" * 30
    
    # Input sequence (word embeddings)
    sequence = [
      [0.1, 0.2, 0.3],  # word 1
      [0.4, 0.5, 0.6],  # word 2
      [0.7, 0.8, 0.9],  # word 3
      [0.2, 0.3, 0.4]   # word 4
    ]
    
    puts "Input sequence (embeddings):"
    sequence.each_with_index do |embedding, i|
      puts "  Word #{i + 1}: #{embedding.map { |v| v.round(2) }}"
    end
    
    # Calculate attention scores
    puts "\nAttention Scores:"
    puts "-" * 30
    
    attention_scores = Array.new(sequence.length) do |i|
      Array.new(sequence.length) do |j|
        # Dot product attention
        sequence[i].zip(sequence[j]).sum { |a, b| a * b }
      end
    end
    
    attention_scores.each_with_index do |scores, i|
      puts "  From word #{i + 1}: #{scores.map { |s| s.round(2) }}"
    end
    
    # Apply softmax
    puts "\nAttention Weights (Softmax):"
    puts "-" * 30
    
    attention_weights = attention_scores.map do |scores|
      exp_scores = scores.map { |s| Math.exp(s) }
      sum_exp = exp_scores.sum
      exp_scores.map { |s| s / sum_exp }
    end
    
    attention_weights.each_with_index do |weights, i|
      puts "  From word #{i + 1}: #{weights.map { |w| w.round(3) }}"
    end
    
    # Weighted sum
    puts "\nAttention Output:"
    puts "-" * 30
    
    attention_output = Array.new(sequence[0].length, 0)
    
    attention_output.each_with_index do |_, dim|
      dim_sum = 0
      
      attention_weights.each_with_index do |weights, i|
        weighted = weights.map { |w| w * sequence[i][dim] }
        dim_sum += weighted.sum
      end
      
      attention_output[dim] = dim_sum
    end
    
    puts "  Output: #{attention_output.map { |v| v.round(3) }}"
    
    # Multi-head attention concept
    puts "\nMulti-Head Attention:"
    puts "-" * 30
    puts "Concept: Split attention into multiple 'heads'"
    puts "Each head learns different attention patterns"
    puts "Combine outputs from all heads"
    puts "Benefits: Parallel processing, diverse representations"
    
    # Cross-attention example
    puts "\nCross-Attention Example:"
    puts "-" * 30
    
    query = [0.1, 0.2, 0.3]    # Current token
    keys = sequence              # All tokens as keys
    values = sequence             # All tokens as values
    
    puts "Query: #{query.map { |v| v.round(2) }}"
    puts "Keys: #{keys.length} tokens"
    puts "Values: #{values.length} tokens"
    
    # Calculate cross-attention
    cross_scores = keys.map { |key| query.zip(key).sum { |q, k| q * k } }
    cross_weights = cross_scores.map { |s| Math.exp(s) / cross_scores.map { |s| Math.exp(s) }.sum }
    
    cross_output = values.zip(cross_weights).map { |val, weight| val.map { |v| v * weight } }.transpose.map { |arr| arr.sum }
    
    puts "Cross-attention output: #{cross_output.map { |v| v.round(3) }}"
    
    puts "\nAttention Applications:"
    puts "- Machine translation (source to target)"
    puts "- Image captioning (image to text)"
    puts "- Document summarization"
    puts "- Question answering"
  end
end

class TransformerBlock
  def initialize(d_model, num_heads)
    @d_model = d_model
    @num_heads = num_heads
    @head_dim = d_model / num_heads
    
    # Initialize attention weights
    @w_q = Array.new(d_model) { Array.new(d_model) { rand * 0.1 - 0.05 } }
    @w_k = Array.new(d_model) { Array.new(d_model) { rand * 0.1 - 0.05 } }
    @w_v = Array.new(d_model) { Array.new(d_model) { rand * 0.1 - 0.05 } }
    @w_o = Array.new(d_model) { Array.new(d_model) { rand * 0.1 - 0.05 } }
    
    # Feed-forward network
    @ffn1 = Array.new(d_model) { Array.new(d_model * 4) { rand * 0.1 - 0.05 } }
    @ffn2 = Array.new(d_model * 4) { Array.new(d_model) { rand * 0.1 - 0.05 } }
    
    # Layer normalization
    @ln1 = LayerNormalizationLayer.new(d_model)
    @ln2 = LayerNormalizationLayer.new(d_model)
  end
  
  def forward(input)
    # Multi-head self-attention
    attention_output = multi_head_attention(input)
    
    # Add & normalize
    x1 = input.zip(attention_output).map { |inp, att| inp.zip(att).map { |i, a| i + a } }
    x1 = @ln1.forward(x1)
    
    # Feed-forward network
    ffn_output = feed_forward_network(x1)
    
    # Add & normalize
    x2 = x1.zip(ffn_output).map { |x1_out, ffn_out| x1_out.zip(ffn_out).map { |x1, ffn| x1 + ffn } }
    x2 = @ln2.forward(x2)
    
    x2
  end
  
  private
  
  def multi_head_attention(input)
    # Split into heads
    heads = Array.new(@num_heads) do |head_idx|
      start_idx = head_idx * @head_dim
      end_idx = start_idx + @head_dim
      
      input.map { |token| token[start_idx...end_idx] }
    end
    
    # Apply attention to each head
    head_outputs = heads.map do |head_input|
      simplified_attention(head_input)
    end
    
    # Concatenate heads
    concatenated = head_outputs.transpose.flatten
    
    # Output projection
    concatenated.map { |token| token.zip(@w_o[0]).sum { |t, w| t * w } }
  end
  
  def simplified_attention(input)
    # Simplified attention for demonstration
    input.map { |token| token.map { |v| ActivationFunctions.sigmoid(v) } }
  end
  
  def feed_forward_network(input)
    # First linear layer
    hidden = input.map { |token| token.map { |v| v * 4 } } # Simplified FFN
    hidden = hidden.map { |token| token.map { |v| ActivationFunctions.relu(v) } }
    
    # Second linear layer
    output = hidden.map { |token| token.map { |v| v * 0.25 } } # Simplified FFN
    
    output
  end
end

class LayerNormalizationLayer
  def initialize(d_model, epsilon = 1e-6)
    @d_model = d_model
    @epsilon = epsilon
    
    @gamma = Array.new(d_model, 1.0)
    @beta = Array.new(d_model, 0.0)
  end
  
  def forward(input)
    # Calculate mean and variance
    mean = input.flatten.sum / input.flatten.length
    variance = input.flatten.sum { |x| (x - mean) ** 2 } / input.flatten.length
    
    # Normalize
    normalized = input.map do |token|
      token.map { |x| (x - mean) / Math.sqrt(variance + @epsilon) }
    end
    
    # Scale and shift
    normalized.map.with_index do |token, i|
      token.map { |x| @gamma[i] * x + @beta[i] }
    end
  end
end

def self.demonstrate_transformer
  puts "Transformer Architecture Demonstration:"
  puts "=" * 50
  
  # Create simple transformer block
  transformer = TransformerBlock.new(12, 4)
  
  # Sample input (sequence of word embeddings)
  sequence = Array.new(5) do
    Array.new(12) { rand * 2 - 1 }
  end
  
  puts "Input sequence:"
  puts "  Sequence length: #{sequence.length}"
  puts "  Embedding dimension: #{sequence[0].length}"
  puts "  Sample token: #{sequence[0].map { |v| v.round(3) }}"
  
  # Forward pass
  output = transformer.forward(sequence)
  
  puts "\nTransformer output:"
  puts "  Output dimension: #{output.length}"
  puts "  Sample output: #{output[0].map { |v| v.round(3) }}"
  
  puts "\nTransformer Components:"
  puts "- Multi-head self-attention"
  puts "- Feed-forward network"
  puts "- Layer normalization"
  puts "- Residual connections"
  
  puts "\nTransformer Applications:"
  puts "- Machine translation (BERT, GPT)"
  puts "- Computer vision (Vision Transformer)"
  puts "- Speech recognition"
  puts "- Natural language processing"
end
```

## 🎯 Deep Learning Applications

### 5. Practical Deep Learning Examples

Real-world applications:

```ruby
class DeepLearningApplications
  def self.image_classification_demo
    puts "Image Classification with Deep Learning:"
    puts "=" * 50
    
    # Simulate CNN for image classification
    class ImageClassifier
      def initialize(num_classes)
        @num_classes = num_classes
        
        # Simulated CNN layers
        @conv1 = ConvolutionalLayer.new(3, 32, 3, 1, 1)
        @pool1 = MaxPoolingLayer.new(2, 2)
        @conv2 = ConvolutionalLayer.new(32, 64, 3, 1, 1)
        @pool2 = MaxPoolingLayer.new(2, 2)
        
        # Calculate flattened size
        @flatten_size = 64 * 7 * 7  # Assuming 28x28 input
        @fc = FullyConnectedLayer.new(@flatten_size, num_classes)
      end
      
      def classify(image)
        # Forward pass
        x = @conv1.forward(image)
        x = x.map { |channel| channel.map { |row| row.map { |val| ActivationFunctions.relu(val) } } }
        x = @pool1.forward(x)
        
        x = @conv2.forward(x)
        x = x.map { |channel| channel.map { |row| row.map { |val| ActivationFunctions.relu(val) } } }
        x = @pool2.forward(x)
        
        # Flatten and classify
        flattened = x.flatten
        logits = @fc.forward(flattened)
        probabilities = ActivationFunctions.softmax(logits)
        
        predicted_class = probabilities.index(probabilities.max)
        confidence = probabilities.max
        
        [predicted_class, confidence]
      end
    end
    
    # Generate sample images
    images = 10.times.map do
      Array.new(3) do
        Array.new(28) do
          Array.new(28) { rand }
        end
      end
    end
    
    # Create classifier
    classifier = ImageClassifier.new(10)
    
    puts "Generated #{images.length} sample images"
    puts "Image size: #{images[0][0].length}x#{images[0][0][0].length}x#{images[0].length}"
    
    # Classify images
    puts "\nImage classifications:"
    images.each_with_index do |image, i|
      predicted_class, confidence = classifier.classify(image)
      puts "Image #{i + 1}: Class #{predicted_class}, Confidence: #{confidence.round(3)}"
    end
    
    puts "\nCNN Features:"
    puts "- Convolutional layers for feature extraction"
    puts "- Pooling layers for spatial reduction"
    puts "- Fully connected layer for classification"
    puts "- Softmax for probability distribution"
  end
  
  def self.sequence_prediction_demo
    puts "\nSequence Prediction with Deep Learning:"
    puts "=" * 50
    
    # Simulate LSTM for sequence prediction
    class SequencePredictor
      def initialize(vocab_size, hidden_size)
        @vocab_size = vocab_size
        @hidden_size = hidden_size
        
        # Embedding layer (simplified)
        @embedding = Array.new(vocab_size) { Array.new(50) { rand * 0.1 - 0.05 } }
        
        # LSTM cell
        @lstm = LSTMCell.new(50, hidden_size)
        
        # Output layer
        @output_layer = FullyConnectedLayer.new(hidden_size, vocab_size)
      end
      
      def predict_next(sequence)
        hidden_state = Array.new(@hidden_size, 0)
        cell_state = Array.new(@hidden_size, 0)
        
        # Process sequence
        sequence.each do |token|
          embedding = @embedding[token]
          hidden_state, cell_state = @lstm.forward(embedding, hidden_state, cell_state)
        end
        
        # Predict next token
        logits = @output_layer.forward(hidden_state)
        probabilities = ActivationFunctions.softmax(logits)
        
        predicted_token = probabilities.index(probabilities.max)
        confidence = probabilities.max
        
        [predicted_token, confidence]
      end
    end
    
    # Generate sample sequences
    sequences = [
      [1, 2, 3, 4, 5],
      [6, 7, 8, 9, 10],
      [11, 12, 13, 14, 15],
      [16, 17, 18, 19, 20]
    ]
    
    # Create predictor
    predictor = SequencePredictor.new(25, 32)
    
    puts "Generated #{sequences.length} sample sequences"
    puts "Vocabulary size: 25"
    puts "Sequence length: #{sequences[0].length}"
    
    # Predict next tokens
    puts "\nSequence predictions:"
    sequences.each_with_index do |sequence, i|
      predicted_token, confidence = predictor.predict_next(sequence)
      
      puts "Sequence #{i + 1}: #{sequence} -> Next: #{predicted_token}, Confidence: #{confidence.round(3)}"
    end
    
    puts "\nLSTM Features:"
    puts "- Memory cells for long-term dependencies"
    puts "- Gates for information flow control"
    puts "- Sequential processing capability"
    puts "- Context-aware predictions"
  end
  
  def self.autoencoder_demo
    puts "\nAutoencoder for Dimensionality Reduction:"
    puts "=" * 50
    
    # Simulate autoencoder
    class Autoencoder
      def initialize(input_size, bottleneck_size)
        @input_size = input_size
        @bottleneck_size = bottleneck_size
        
        # Encoder
        @encoder_fc1 = FullyConnectedLayer.new(input_size, 128)
        @encoder_fc2 = FullyConnectedLayer.new(128, bottleneck_size)
        
        # Decoder
        @decoder_fc1 = FullyConnectedLayer.new(bottleneck_size, 128)
        @decoder_fc2 = FullyConnectedLayer.new(128, input_size)
      end
      
      def encode(input)
        # Encoder forward pass
        x = @encoder_fc1.forward(input)
        x = x.map { |val| ActivationFunctions.relu(val) }
        x = @encoder_fc2.forward(x)
        x = x.map { |val| ActivationFunctions.relu(val) }
        
        x
      end
      
      def decode(encoded)
        # Decoder forward pass
        x = @decoder_fc1.forward(encoded)
        x = x.map { |val| ActivationFunctions.relu(val) }
        x = @decoder_fc2.forward(x)
        
        x
      end
      
      def forward(input)
        encoded = encode(input)
        decoded = decode(encoded)
        decoded
      end
      
      def calculate_reconstruction_error(input, reconstructed)
        input.zip(reconstructed).sum { |original, reconstructed_val| (original - reconstructed_val) ** 2 } / input.length
      end
    end
    
    # Generate sample data
    data = 20.times.map { Array.new(100) { rand * 2 - 1 } }
    
    # Create autoencoder
    autoencoder = Autoencoder.new(100, 10)
    
    puts "Generated #{data.length} data samples"
    puts "Input dimension: #{data[0].length}"
    puts "Bottleneck dimension: 10"
    
    # Encode and decode data
    puts "\nReconstruction examples:"
    5.times do |i|
      input = data[i]
      reconstructed = autoencoder.forward(input)
      error = autoencoder.calculate_reconstruction_error(input, reconstructed)
      
      puts "Sample #{i + 1}: Error = #{error.round(4)}"
    end
    
    # Show bottleneck representation
    puts "\nBottleneck representations:"
    3.times do |i|
      input = data[i]
      encoded = autoencoder.encode(input)
      
      puts "Sample #{i + 1}: #{encoded.map { |v| v.round(3) }}"
    end
    
    puts "\nAutoencoder Applications:"
    puts "- Dimensionality reduction"
    puts "- Feature learning"
    puts "- Anomaly detection"
    puts "- Data denoising"
    puts "- Pretraining for other tasks"
  end
  
  # Run all demonstrations
  image_classification_demo
  sequence_prediction_demo
  autoencoder_demo
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Deep Network**: Build simple deep neural network
2. **ResNet Block**: Implement residual block
3. **LSTM Cell**: Create LSTM memory cell
4. **Attention**: Implement attention mechanism

### Intermediate Exercises

1. **CNN**: Build convolutional neural network
2. **RNN**: Create recurrent neural network
3. **Transformer**: Implement transformer block
4. **Autoencoder**: Build autoencoder architecture

### Advanced Exercises

1. **Advanced CNN**: Implement ResNet architecture
2. **Sequence Models**: Build complex RNN models
3. **Attention Models**: Create attention-based models
4. **Real Applications**: Apply to real datasets

---

## 🎯 Summary

Deep Learning in Ruby provides:

- **Deep Learning Concepts** - Advanced neural network concepts
- **Residual Networks** - Deep networks with skip connections
- **Recurrent Networks** - Memory-based sequence models
- **Attention Mechanisms** - Self-attention and transformers
- **Advanced Applications** - Real-world deep learning examples
- **Practical Implementations** - Working deep learning code

Master these advanced concepts to build sophisticated AI models in Ruby!
