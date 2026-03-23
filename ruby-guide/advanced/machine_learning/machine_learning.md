# Machine Learning with Ruby

## Overview

While Python dominates the machine learning landscape, Ruby has excellent libraries and frameworks for ML tasks. This guide covers data processing, machine learning algorithms, neural networks, and practical ML applications in Ruby.

## Data Processing

### Numo::NArray for Numerical Computing

```ruby
require 'numo/narray'

# Basic array operations
class DataProcessor
  def initialize
    @data = nil
  end
  
  def load_data(file_path)
    # Load CSV data into Numo array
    require 'csv'
    
    data = []
    headers = []
    
    CSV.foreach(file_path) do |row|
      if headers.empty?
        headers = row
      else
        data << row.map(&:to_f)
      end
    end
    
    @data = Numo::NArray.cast(data)
    @headers = headers
    @data
  end
  
  def normalize_data
    # Min-max normalization
    min_values = @data.min(0)
    max_values = @data.max(0)
    range = max_values - min_values
    
    # Avoid division by zero
    range[range.eq(0)] = 1
    
    normalized = (@data - min_values) / range
    normalized
  end
  
  def standardize_data
    # Z-score standardization
    mean = @data.mean(0)
    std = @data.std(0)
    
    # Avoid division by zero
    std[std.eq(0)] = 1
    
    standardized = (@data - mean) / std
    standardized
  end
  
  def split_data(train_ratio = 0.8)
    n_samples = @data.shape[0]
    n_train = (n_samples * train_ratio).to_i
    
    indices = (0...n_samples).to_a.shuffle
    train_indices = indices[0...n_train]
    test_indices = indices[n_train..-1]
    
    train_data = @data[train_indices, true]
    test_data = @data[test_indices, true]
    
    [train_data, test_data]
  end
  
  def feature_selection(target_column = -1)
    # Separate features and target
    features = @data[true, 0...target_column]
    target = @data[true, target_column]
    
    [features, target]
  end
  
  def correlation_matrix
    # Calculate correlation matrix
    mean_centered = @data - @data.mean(0)
    covariance = mean_centered.transpose.dot(mean_centered) / (@data.shape[0] - 1)
    
    std_dev = Numo::NArray.sqrt(@data.variance(0))
    correlation = covariance / (std_dev.expand_dims(1).dot(std_dev.expand_dims(0)))
    
    correlation
  end
end

# Usage
processor = DataProcessor.new
data = processor.load_data('data.csv')
normalized = processor.normalize_data
train_data, test_data = processor.split_data
```

### Data Visualization

```ruby
require 'gruff'

class DataVisualizer
  def initialize
    @charts = {}
  end
  
  def plot_histogram(data, title = "Histogram", bins = 20)
    chart = Gruff::Histogram.new
    chart.title = title
    chart.data = data.to_a.flatten
    chart.bins = bins
    
    filename = "histogram_#{Time.now.to_i}.png"
    chart.write(filename)
    filename
  end
  
  def plot_scatter(x_data, y_data, title = "Scatter Plot")
    chart = Gruff::Scatter.new
    chart.title = title
    chart.data("Points", x_data.zip(y_data))
    
    filename = "scatter_#{Time.now.to_i}.png"
    chart.write(filename)
    filename
  end
  
  def plot_line(x_data, y_data, title = "Line Plot")
    chart = Gruff::Line.new
    chart.title = title
    chart.data("Data", y_data.to_a.flatten)
    chart.labels = Hash[(0...x_data.length).map { |i| [i, x_data[i].to_s] }]
    
    filename = "line_#{Time.now.to_i}.png"
    chart.write(filename)
    filename
  end
  
  def plot_bar(categories, values, title = "Bar Chart")
    chart = Gruff::Bar.new
    chart.title = title
    chart.data("Values", values.to_a.flatten)
    chart.labels = Hash[(0...categories.length).map { |i| [i, categories[i].to_s] }]
    
    filename = "bar_#{Time.now.to_i}.png"
    chart.write(filename)
    filename
  end
  
  def plot_correlation_matrix(correlation_matrix, feature_names)
    # Create heatmap using scatter plot
    n_features = correlation_matrix.shape[0]
    chart = Gruff::Scatter.new
    chart.title = "Correlation Matrix"
    
    (0...n_features).each do |i|
      (0...n_features).each do |j|
        chart.data("#{feature_names[i]}-#{feature_names[j]}", [[i, j, correlation_matrix[i, j]]])
      end
    end
    
    filename = "correlation_#{Time.now.to_i}.png"
    chart.write(filename)
    filename
  end
end
```

## Machine Learning Algorithms

### Linear Regression

```ruby
class LinearRegression
  def initialize(learning_rate = 0.01, epochs = 1000)
    @learning_rate = learning_rate
    @epochs = epochs
    @weights = nil
    @bias = nil
  end
  
  def fit(X, y)
    n_samples, n_features = X.shape
    
    # Initialize weights and bias
    @weights = Numo::SFloat.zeros(n_features)
    @bias = 0.0
    
    # Gradient descent
    @epochs.times do |epoch|
      # Forward pass
      y_predicted = X.dot(@weights) + @bias
      
      # Calculate gradients
      dw = (1.0 / n_samples) * X.transpose.dot(y_predicted - y)
      db = (1.0 / n_samples) * (y_predicted - y).sum
      
      # Update weights
      @weights -= @learning_rate * dw
      @bias -= @learning_rate * db
      
      # Print progress
      if epoch % 100 == 0
        loss = calculate_loss(X, y)
        puts "Epoch #{epoch}, Loss: #{loss}"
      end
    end
  end
  
  def predict(X)
    X.dot(@weights) + @bias
  end
  
  def score(X, y)
    y_pred = predict(X)
    ss_res = ((y - y_pred) ** 2).sum
    ss_tot = ((y - y.mean) ** 2).sum
    1 - (ss_res / ss_tot)
  end
  
  private
  
  def calculate_loss(X, y)
    y_predicted = X.dot(@weights) + @bias
    ((y_predicted - y) ** 2).mean
  end
end

# Usage
# Generate sample data
X = Numo::SFloat.new(100, 1).rand
y = 2 * X.flatten + 1 + Numo::SFloat.new(100).rand * 0.1

# Split data
train_size = 80
X_train = X[0...train_size, true]
y_train = y[0...train_size]
X_test = X[train_size..-1, true]
y_test = y[train_size..-1]

# Train model
model = LinearRegression.new(0.01, 1000)
model.fit(X_train, y_train)

# Evaluate
score = model.score(X_test, y_test)
puts "R² Score: #{score}"

# Make predictions
predictions = model.predict(X_test)
```

### Logistic Regression

```ruby
class LogisticRegression
  def initialize(learning_rate = 0.01, epochs = 1000)
    @learning_rate = learning_rate
    @epochs = epochs
    @weights = nil
    @bias = nil
  end
  
  def fit(X, y)
    n_samples, n_features = X.shape
    
    # Initialize weights and bias
    @weights = Numo::SFloat.zeros(n_features)
    @bias = 0.0
    
    # Gradient descent
    @epochs.times do |epoch|
      # Forward pass
      linear_model = X.dot(@weights) + @bias
      y_predicted = sigmoid(linear_model)
      
      # Calculate gradients
      dw = (1.0 / n_samples) * X.transpose.dot(y_predicted - y)
      db = (1.0 / n_samples) * (y_predicted - y).sum
      
      # Update weights
      @weights -= @learning_rate * dw
      @bias -= @learning_rate * db
      
      # Print progress
      if epoch % 100 == 0
        loss = calculate_loss(X, y)
        puts "Epoch #{epoch}, Loss: #{loss}"
      end
    end
  end
  
  def predict(X)
    linear_model = X.dot(@weights) + @bias
    y_predicted = sigmoid(linear_model)
    y_predicted.round
  end
  
  def predict_proba(X)
    linear_model = X.dot(@weights) + @bias
    y_predicted = sigmoid(linear_model)
    
    # Return probabilities for both classes
    class_0 = 1 - y_predicted
    class_1 = y_predicted
    
    Numo::NArray.hstack([class_0, class_1])
  end
  
  def score(X, y)
    y_pred = predict(X)
    (y_pred == y).count.to_f / y.length
  end
  
  private
  
  def sigmoid(z)
    1.0 / (1.0 + Numo::NMath.exp(-z))
  end
  
  def calculate_loss(X, y)
    linear_model = X.dot(@weights) + @bias
    y_predicted = sigmoid(linear_model)
    
    # Avoid log(0)
    epsilon = 1e-15
    y_predicted = Numo::SFloat.clip(y_predicted, epsilon, 1 - epsilon)
    
    (-y * Numo::NMath.log(y_predicted) - (1 - y) * Numo::NMath.log(1 - y_predicted)).mean
  end
end

# Usage
# Generate sample binary classification data
X = Numo::SFloat.new(200, 2).rand * 10
y = ((X[:, 0] + X[:, 1]) > 10).to_i

# Split data
train_size = 160
X_train = X[0...train_size, true]
y_train = y[0...train_size]
X_test = X[train_size..-1, true]
y_test = y[train_size..-1]

# Train model
model = LogisticRegression.new(0.01, 1000)
model.fit(X_train, y_train)

# Evaluate
accuracy = model.score(X_test, y_test)
puts "Accuracy: #{accuracy}"

# Make predictions
predictions = model.predict(X_test)
probabilities = model.predict_proba(X_test)
```

### K-Means Clustering

```ruby
class KMeans
  def initialize(k, max_iterations = 100)
    @k = k
    @max_iterations = max_iterations
    @centroids = nil
    @labels = nil
  end
  
  def fit(X)
    n_samples, n_features = X.shape
    
    # Initialize centroids randomly
    indices = (0...n_samples).to_a.sample(@k)
    @centroids = X[indices, true]
    
    @max_iterations.times do |iteration|
      # Assign samples to closest centroids
      @labels = assign_clusters(X)
      
      # Update centroids
      new_centroids = update_centroids(X)
      
      # Check for convergence
      if converged?(@centroids, new_centroids)
        puts "Converged after #{iteration} iterations"
        break
      end
      
      @centroids = new_centroids
    end
  end
  
  def predict(X)
    assign_clusters(X)
  end
  
  def inertia
    return 0 if @centroids.nil? || @labels.nil?
    
    total_inertia = 0
    @labels.each_with_index do |label, i|
      centroid = @centroids[label, true]
      total_inertia += ((X[i, true] - centroid) ** 2).sum
    end
    total_inertia
  end
  
  private
  
  def assign_clusters(X)
    labels = Numo::Int32.zeros(X.shape[0])
    
    X.shape[0].times do |i|
      distances = []
      @k.times do |k|
        centroid = @centroids[k, true]
        distance = ((X[i, true] - centroid) ** 2).sum
        distances << distance
      end
      labels[i] = distances.index(distances.min)
    end
    
    labels
  end
  
  def update_centroids(X)
    new_centroids = Numo::SFloat.zeros(@k, X.shape[1])
    
    @k.times do |k|
      cluster_points = X[@labels.eq(k), true]
      if cluster_points.shape[0] > 0
        new_centroids[k, true] = cluster_points.mean(0)
      else
        new_centroids[k, true] = @centroids[k, true]
      end
    end
    
    new_centroids
  end
  
  def converged?(old_centroids, new_centroids)
    ((old_centroids - new_centroids) ** 2).sum < 1e-6
  end
end

# Usage
# Generate sample clustering data
X = Numo::SFloat.new(300, 2).rand * 10

# Add some structure to the data
X[0...100, 0] += 5
X[100...200, 1] += 5
X[200...300] += 3

# Apply K-means
kmeans = KMeans.new(3)
kmeans.fit(X)

# Get cluster assignments
labels = kmeans.predict(X)
inertia = kmeans.inertia

puts "Cluster inertia: #{inertia}"
puts "Cluster assignments: #{labels.to_a.uniq}"
```

### Decision Tree

```ruby
class DecisionTree
  def initialize(max_depth = 10, min_samples_split = 2)
    @max_depth = max_depth
    @min_samples_split = min_samples_split
    @tree = nil
  end
  
  def fit(X, y)
    @tree = build_tree(X, y, 0)
  end
  
  def predict(X)
    predictions = []
    
    X.shape[0].times do |i|
      prediction = predict_single(X[i, true], @tree)
      predictions << prediction
    end
    
    Numo::Int32.cast(predictions)
  end
  
  private
  
  def build_tree(X, y, depth)
    # Base cases
    return create_leaf_node(y) if depth >= @max_depth
    return create_leaf_node(y) if y.uniq.length == 1
    return create_leaf_node(y) if X.shape[0] < @min_samples_split
    
    # Find best split
    best_feature, best_threshold, best_gain = find_best_split(X, y)
    
    # If no improvement, create leaf
    if best_gain <= 0
      return create_leaf_node(y)
    end
    
    # Split data
    left_indices = X[:, best_feature] <= best_threshold
    right_indices = X[:, best_feature] > best_threshold
    
    # Build subtrees
    left_tree = build_tree(X[left_indices, true], y[left_indices], depth + 1)
    right_tree = build_tree(X[right_indices, true], y[right_indices], depth + 1)
    
    # Create internal node
    {
      feature: best_feature,
      threshold: best_threshold,
      gain: best_gain,
      left: left_tree,
      right: right_tree
    }
  end
  
  def find_best_split(X, y)
    best_feature = nil
    best_threshold = nil
    best_gain = 0
    
    n_features = X.shape[1]
    parent_entropy = calculate_entropy(y)
    
    n_features.times do |feature|
      unique_values = X[:, feature].uniq.sort
      
      unique_values.each do |threshold|
        # Split data
        left_indices = X[:, feature] <= threshold
        right_indices = X[:, feature] > threshold
        
        next if left_indices.sum == 0 || right_indices.sum == 0
        
        # Calculate information gain
        left_entropy = calculate_entropy(y[left_indices])
        right_entropy = calculate_entropy(y[right_indices])
        
        left_weight = left_indices.sum.to_f / X.shape[0]
        right_weight = right_indices.sum.to_f / X.shape[0]
        
        weighted_entropy = left_weight * left_entropy + right_weight * right_entropy
        information_gain = parent_entropy - weighted_entropy
        
        # Update best split
        if information_gain > best_gain
          best_gain = information_gain
          best_feature = feature
          best_threshold = threshold
        end
      end
    end
    
    [best_feature, best_threshold, best_gain]
  end
  
  def calculate_entropy(y)
    return 0 if y.length == 0
    
    classes, counts = y.uniq, y.uniq.map { |c| (y == c).sum }
    probabilities = counts.map { |count| count.to_f / y.length }
    
    -probabilities.map { |p| p * Math.log2(p) }.sum
  end
  
  def create_leaf_node(y)
    most_common = y.uniq.max_by { |c| (y == c).sum }
    { class: most_common, count: y.length }
  end
  
  def predict_single(x, node)
    return node[:class] if node.key?(:class)
    
    if x[node[:feature]] <= node[:threshold]
      predict_single(x, node[:left])
    else
      predict_single(x, node[:right])
    end
  end
end

# Usage
# Generate sample classification data
X = Numo::SFloat.new(200, 2).rand * 10
y = ((X[:, 0] + X[:, 1]) > 10).to_i

# Split data
train_size = 160
X_train = X[0...train_size, true]
y_train = y[0...train_size]
X_test = X[train_size..-1, true]
y_test = y[train_size..-1]

# Train model
tree = DecisionTree.new(5, 2)
tree.fit(X_train, y_train)

# Make predictions
predictions = tree.predict(X_test)
accuracy = (predictions == y_test).count.to_f / y_test.length

puts "Decision Tree Accuracy: #{accuracy}"
```

## Neural Networks

### Simple Neural Network

```ruby
class NeuralNetwork
  def initialize(layer_sizes, learning_rate = 0.01)
    @layer_sizes = layer_sizes
    @learning_rate = learning_rate
    @weights = []
    @biases = []
    
    # Initialize weights and biases
    (layer_sizes.length - 1).times do |i|
      @weights << Numo::SFloat.new(layer_sizes[i + 1], layer_sizes[i]).rand * 0.1
      @biases << Numo::SFloat.zeros(layer_sizes[i + 1])
    end
  end
  
  def forward(X)
    @activations = [X]
    current_input = X
    
    @weights.each_with_index do |weights, i|
      z = weights.dot(current_input) + @biases[i]
      activation = sigmoid(z)
      @activations << activation
      current_input = activation
    end
    
    @activations.last
  end
  
  def backward(X, y)
    m = X.shape[0]
    
    # Forward pass
    output = forward(X)
    
    # Calculate gradients
    deltas = []
    
    # Output layer gradient
    delta = (output - y) * sigmoid_derivative(@activations.last)
    deltas << delta
    
    # Hidden layers gradients
    ( @weights.length - 1 ).downto(0) do |l|
      if l > 0
        delta = @weights[l].transpose.dot(delta) * sigmoid_derivative(@activations[l])
        deltas << delta
      end
    end
    
    # Update weights and biases
    deltas.reverse!
    
    @weights.each_with_index do |weights, i|
      if i == 0
        dw = deltas[i].dot(X.transpose) / m
      else
        dw = deltas[i].dot(@activations[i].transpose) / m
      end
      
      db = deltas[i].mean(1)
      
      @weights[i] -= @learning_rate * dw
      @biases[i] -= @learning_rate * db
    end
  end
  
  def train(X, y, epochs = 1000)
    epochs.times do |epoch|
      # Forward and backward pass
      output = forward(X)
      backward(X, y)
      
      # Calculate loss
      loss = ((output - y) ** 2).mean
      
      # Print progress
      if epoch % 100 == 0
        puts "Epoch #{epoch}, Loss: #{loss}"
      end
    end
  end
  
  def predict(X)
    output = forward(X)
    output.round
  end
  
  def score(X, y)
    predictions = predict(X)
    (predictions == y).count.to_f / y.length
  end
  
  private
  
  def sigmoid(z)
    1.0 / (1.0 + Numo::NMath.exp(-z))
  end
  
  def sigmoid_derivative(a)
    a * (1 - a)
  end
end

# Usage
# Generate sample data for XOR problem
X = Numo::SFloat[[0, 0], [0, 1], [1, 0], [1, 1]]
y = Numo::SFloat[[0], [1], [1], [0]]

# Create neural network
nn = NeuralNetwork.new([2, 4, 1], 0.1)

# Train network
nn.train(X, y, 5000)

# Test network
predictions = nn.predict(X)
accuracy = nn.score(X, y)

puts "Neural Network Accuracy: #{accuracy}"
puts "Predictions: #{predictions.to_a.flatten}"
```

### Deep Learning Framework

```ruby
class DeepLearningFramework
  def initialize
    @layers = []
    @loss_function = nil
    @optimizer = nil
  end
  
  def add_layer(layer)
    @layers << layer
  end
  
  def set_loss_function(loss_function)
    @loss_function = loss_function
  end
  
  def set_optimizer(optimizer)
    @optimizer = optimizer
  end
  
  def compile
    # Initialize layers
    @layers.each_with_index do |layer, i|
      if i > 0
        layer.initialize(@layers[i - 1].output_size)
      end
    end
  end
  
  def train(X, y, epochs = 100, batch_size = 32)
    n_samples = X.shape[0]
    
    epochs.times do |epoch|
      # Shuffle data
      indices = (0...n_samples).to_a.shuffle
      
      # Mini-batch training
      (0...n_samples).step(batch_size) do |start|
        batch_indices = indices[start...[start + batch_size, n_samples].min]
        X_batch = X[batch_indices, true]
        y_batch = y[batch_indices]
        
        # Forward pass
        output = forward_pass(X_batch)
        
        # Backward pass
        backward_pass(X_batch, y_batch)
        
        # Update weights
        update_weights
      end
      
      # Calculate and print loss
      if epoch % 10 == 0
        loss = calculate_loss(X, y)
        puts "Epoch #{epoch}, Loss: #{loss}"
      end
    end
  end
  
  def predict(X)
    forward_pass(X)
  end
  
  private
  
  def forward_pass(X)
    current_input = X
    
    @layers.each do |layer|
      current_input = layer.forward(current_input)
    end
    
    current_input
  end
  
  def backward_pass(X, y)
    # Forward pass to get activations
    activations = [X]
    current_input = X
    
    @layers.each do |layer|
      current_input = layer.forward(current_input)
      activations << current_input
    end
    
    # Calculate initial gradient
    gradient = @loss_function.gradient(activations.last, y)
    
    # Backward pass through layers
    @layers.reverse.each_with_index do |layer, i|
      activation = activations[@layers.length - i - 1]
      gradient = layer.backward(gradient, activation)
    end
  end
  
  def update_weights
    @layers.each do |layer|
      @optimizer.update(layer)
    end
  end
  
  def calculate_loss(X, y)
    predictions = forward_pass(X)
    @loss_function.loss(predictions, y)
  end
end

# Layer classes
class DenseLayer
  attr_reader :output_size
  
  def initialize(units, activation = :sigmoid)
    @units = units
    @activation = activation
    @weights = nil
    @biases = nil
    @input_size = nil
    @output_size = units
    @dweights = nil
    @dbiases = nil
    @input = nil
    @output = nil
  end
  
  def initialize(input_size)
    @input_size = input_size
    @weights = Numo::SFloat.new(@units, input_size).rand * 0.1
    @biases = Numo::SFloat.zeros(@units)
  end
  
  def forward(input)
    @input = input
    z = @weights.dot(input) + @biases
    @output = apply_activation(z)
    @output
  end
  
  def backward(gradient, activation)
    # Calculate gradients
    activation_gradient = activation_derivative(activation)
    dz = gradient * activation_gradient
    
    @dweights = dz.dot(@input.transpose)
    @dbiases = dz.mean(1)
    
    # Return gradient for previous layer
    @weights.transpose.dot(dz)
  end
  
  private
  
  def apply_activation(z)
    case @activation
    when :sigmoid
      1.0 / (1.0 + Numo::NMath.exp(-z))
    when :relu
      Numo::SFloat.maximum(0, z)
    when :tanh
      Numo::NMath.tanh(z)
    else
      z
    end
  end
  
  def activation_derivative(activation)
    case @activation
    when :sigmoid
      activation * (1 - activation)
    when :relu
      (activation > 0).cast_to(Numo::SFloat)
    when :tanh
      1 - activation ** 2
    else
      Numo::SFloat.ones(activation.shape)
    end
  end
end

# Loss functions
class MeanSquaredError
  def loss(predictions, targets)
    ((predictions - targets) ** 2).mean
  end
  
  def gradient(predictions, targets)
    2 * (predictions - targets) / predictions.shape[0]
  end
end

class BinaryCrossentropy
  def loss(predictions, targets)
    epsilon = 1e-15
    predictions = Numo::SFloat.clip(predictions, epsilon, 1 - epsilon)
    
    (-targets * Numo::NMath.log(predictions) - (1 - targets) * Numo::NMath.log(1 - predictions)).mean
  end
  
  def gradient(predictions, targets)
    epsilon = 1e-15
    predictions = Numo::SFloat.clip(predictions, epsilon, 1 - epsilon)
    
    (predictions - targets) / (predictions * (1 - predictions))
  end
end

# Optimizers
class SGD
  def initialize(learning_rate = 0.01)
    @learning_rate = learning_rate
  end
  
  def update(layer)
    layer.instance_variable_set(:@weights, 
      layer.instance_variable_get(:@weights) - @learning_rate * layer.instance_variable_get(:@dweights))
    layer.instance_variable_set(:@biases, 
      layer.instance_variable_get(:@biases) - @learning_rate * layer.instance_variable_get(:@dbiases))
  end
end

# Usage
framework = DeepLearningFramework.new
framework.add_layer(DenseLayer.new(4, :sigmoid))
framework.add_layer(DenseLayer.new(1, :sigmoid))
framework.set_loss_function(BinaryCrossentropy.new)
framework.set_optimizer(SGD.new(0.1))
framework.compile

# Train on XOR data
X = Numo::SFloat[[0, 0], [0, 1], [1, 0], [1, 1]]
y = Numo::SFloat[[0], [1], [1], [0]]

framework.train(X, y, 1000, 4)

predictions = framework.predict(X)
puts "Predictions: #{predictions.to_a.flatten}"
```

## Real-World Applications

### Sentiment Analysis

```ruby
class SentimentAnalyzer
  def initialize
    @vocabulary = {}
    @word_weights = {}
    @bias = 0.0
  end
  
  def train(texts, labels, epochs = 100)
    # Build vocabulary
    build_vocabulary(texts)
    
    # Convert texts to feature vectors
    X = texts_to_vectors(texts)
    y = Numo::SFloat.cast(labels)
    
    # Train logistic regression
    model = LogisticRegression.new(0.01, epochs)
    model.fit(X, y)
    
    # Store model parameters
    @word_weights = model.instance_variable_get(:@weights)
    @bias = model.instance_variable_get(:@bias)
  end
  
  def predict(text)
    vector = text_to_vector(text)
    prediction = @word_weights.dot(vector) + @bias
    sigmoid(prediction) > 0.5 ? :positive : :negative
  end
  
  def predict_proba(text)
    vector = text_to_vector(text)
    prediction = @word_weights.dot(vector) + @bias
    probability = sigmoid(prediction)
    
    {
      negative: 1 - probability,
      positive: probability
    }
  end
  
  private
  
  def build_vocabulary(texts)
    word_count = Hash.new(0)
    
    texts.each do |text|
      words = tokenize(text)
      words.each { |word| word_count[word] += 1 }
    end
    
    # Keep only words that appear at least 3 times
    @vocabulary = word_count.select { |_, count| count >= 3 }.keys
    @vocabulary.each_with_index do |word, index|
      @vocabulary[word] = index
    end
  end
  
  def tokenize(text)
    text.downcase.gsub(/[^a-z\s]/, '').split
  end
  
  def texts_to_vectors(texts)
    vectors = []
    
    texts.each do |text|
      vector = Numo::SFloat.zeros(@vocabulary.length)
      words = tokenize(text)
      
      words.each do |word|
        if @vocabulary.key?(word)
          index = @vocabulary[word]
          vector[index] += 1
        end
      end
      
      vectors << vector
    end
    
    Numo::SFloat.vstack(vectors)
  end
  
  def text_to_vector(text)
    vector = Numo::SFloat.zeros(@vocabulary.length)
    words = tokenize(text)
    
    words.each do |word|
      if @vocabulary.key?(word)
        index = @vocabulary[word]
        vector[index] += 1
      end
    end
    
    vector
  end
  
  def sigmoid(z)
    1.0 / (1.0 + Math.exp(-z))
  end
end

# Usage
# Training data
training_texts = [
  "I love this product! It's amazing!",
  "This is the worst thing ever. Terrible!",
  "Great quality and fast delivery",
  "Poor customer service, very disappointed",
  "Excellent value for money",
  "Complete waste of time and money"
]

training_labels = [1, 0, 1, 0, 1, 0]  # 1 for positive, 0 for negative

# Train sentiment analyzer
analyzer = SentimentAnalyzer.new
analyzer.train(training_texts, training_labels, 100)

# Test sentiment analyzer
test_texts = [
  "I really like this item",
  "This is not good at all",
  "Amazing product, highly recommended",
  "Very bad experience"
]

test_texts.each do |text|
  sentiment = analyzer.predict(text)
  probabilities = analyzer.predict_proba(text)
  
  puts "Text: #{text}"
  puts "Sentiment: #{sentiment}"
  puts "Probabilities: #{probabilities}"
  puts "---"
end
```

### Recommendation System

```ruby
class RecommendationSystem
  def initialize
    @user_item_matrix = nil
    @user_means = nil
    @item_means = nil
    @user_similarities = nil
    @item_similarities = nil
  end
  
  def fit(ratings)
    # Convert ratings to user-item matrix
    @user_item_matrix = build_user_item_matrix(ratings)
    
    # Calculate user and item means
    @user_means = @user_item_matrix.mean(1, keepdims: true)
    @item_means = @user_item_matrix.mean(0, keepdims: true)
    
    # Calculate similarities
    calculate_user_similarities
    calculate_item_similarities
  end
  
  def predict_user_based(user_id, item_id, k = 5)
    return 0 if user_id >= @user_item_matrix.shape[0] || item_id >= @user_item_matrix.shape[1]
    
    # Find similar users
    similarities = @user_similarities[user_id, true]
    similar_users = find_top_k(similarities, k)
    
    # Weighted average of ratings
    numerator = 0.0
    denominator = 0.0
    
    similar_users.each do |similar_user_id, similarity|
      rating = @user_item_matrix[similar_user_id, item_id]
      next if rating == 0
      
      user_mean = @user_means[similar_user_id]
      numerator += similarity * (rating - user_mean)
      denominator += similarity.abs
    end
    
    if denominator > 0
      predicted = @user_means[user_id] + (numerator / denominator)
      [predicted, 5].min.round
    else
      @item_means[item_id].round
    end
  end
  
  def predict_item_based(user_id, item_id, k = 5)
    return 0 if user_id >= @user_item_matrix.shape[0] || item_id >= @user_item_matrix.shape[1]
    
    # Find similar items
    similarities = @item_similarities[item_id, true]
    similar_items = find_top_k(similarities, k)
    
    # Weighted average of ratings
    numerator = 0.0
    denominator = 0.0
    
    similar_items.each do |similar_item_id, similarity|
      rating = @user_item_matrix[user_id, similar_item_id]
      next if rating == 0
      
      item_mean = @item_means[similar_item_id]
      numerator += similarity * (rating - item_mean)
      denominator += similarity.abs
    end
    
    if denominator > 0
      predicted = @item_means[item_id] + (numerator / denominator)
      [predicted, 5].min.round
    else
      @user_means[user_id].round
    end
  end
  
  def recommend_items(user_id, n = 5, method = :user_based)
    return [] if user_id >= @user_item_matrix.shape[0]
    
    # Get items not rated by user
    user_ratings = @user_item_matrix[user_id, true]
    unrated_items = (0...user_ratings.length).select { |i| user_ratings[i] == 0 }
    
    # Predict ratings for unrated items
    predictions = []
    
    unrated_items.each do |item_id|
      if method == :user_based
        rating = predict_user_based(user_id, item_id)
      else
        rating = predict_item_based(user_id, item_id)
      end
      
      predictions << [item_id, rating]
    end
    
    # Sort by predicted rating and return top n
    predictions.sort_by { |_, rating| -rating }.first(n)
  end
  
  private
  
  def build_user_item_matrix(ratings)
    # Find max user and item IDs
    max_user_id = ratings.map { |r| r[:user_id] }.max + 1
    max_item_id = ratings.map { |r| r[:item_id] }.max + 1
    
    # Create matrix
    matrix = Numo::SFloat.zeros(max_user_id, max_item_id)
    
    # Fill matrix with ratings
    ratings.each do |rating|
      matrix[rating[:user_id], rating[:item_id]] = rating[:rating]
    end
    
    matrix
  end
  
  def calculate_user_similarities
    n_users = @user_item_matrix.shape[0]
    @user_similarities = Numo::SFloat.zeros(n_users, n_users)
    
    n_users.times do |i|
      n_users.times do |j|
        next if i == j
        
        # Calculate cosine similarity
        similarity = cosine_similarity(@user_item_matrix[i, true], @user_item_matrix[j, true])
        @user_similarities[i, j] = similarity
        @user_similarities[j, i] = similarity
      end
    end
  end
  
  def calculate_item_similarities
    n_items = @user_item_matrix.shape[1]
    @item_similarities = Numo::SFloat.zeros(n_items, n_items)
    
    n_items.times do |i|
      n_items.times do |j|
        next if i == j
        
        # Calculate cosine similarity
        similarity = cosine_similarity(@user_item_matrix[true, i], @user_item_matrix[true, j])
        @item_similarities[i, j] = similarity
        @item_similarities[j, i] = similarity
      end
    end
  end
  
  def cosine_similarity(vector1, vector2)
    dot_product = vector1.dot(vector2)
    norm1 = Numo::NMath.sqrt((vector1 ** 2).sum)
    norm2 = Numo::NMath.sqrt((vector2 ** 2).sum)
    
    return 0 if norm1 == 0 || norm2 == 0
    
    dot_product / (norm1 * norm2)
  end
  
  def find_top_k(similarities, k)
    # Convert to array and sort
    sim_array = similarities.to_a
    sim_array.each_with_index.map { |sim, i| [i, sim] }
                  .sort_by { |_, sim| -sim }
                  .first(k)
  end
end

# Usage
# Sample rating data
ratings = [
  { user_id: 0, item_id: 0, rating: 5 },
  { user_id: 0, item_id: 1, rating: 3 },
  { user_id: 0, item_id: 3, rating: 1 },
  { user_id: 1, item_id: 0, rating: 4 },
  { user_id: 1, item_id: 2, rating: 2 },
  { user_id: 1, item_id: 3, rating: 5 },
  { user_id: 2, item_id: 0, rating: 1 },
  { user_id: 2, item_id: 1, rating: 5 },
  { user_id: 2, item_id: 2, rating: 4 },
  { user_id: 3, item_id: 1, rating: 2 },
  { user_id: 3, item_id: 2, rating: 3 },
  { user_id: 3, item_id: 3, rating: 4 }
]

# Create and train recommendation system
rec_system = RecommendationSystem.new
rec_system.fit(ratings)

# Make predictions
prediction = rec_system.predict_user_based(0, 2)
puts "Predicted rating for user 0, item 2: #{prediction}"

# Get recommendations
recommendations = rec_system.recommend_items(0, 3, :user_based)
puts "Recommendations for user 0:"
recommendations.each { |item_id, rating| puts "  Item #{item_id}: #{rating}" }
```

## Best Practices

### 1. Data Preprocessing

```ruby
# Always normalize/standardize features
# Handle missing values appropriately
# Split data into train/test sets
# Use cross-validation for model evaluation
```

### 2. Model Selection

```ruby
# Start with simple models
# Use appropriate evaluation metrics
# Consider model complexity
# Validate on hold-out data
```

### 3. Feature Engineering

```ruby
# Create meaningful features
# Remove redundant features
# Use domain knowledge
# Experiment with different feature combinations
```

### 4. Model Evaluation

```ruby
# Use multiple evaluation metrics
# Consider business objectives
# Test on real-world data
# Monitor model performance over time
```

## Practice Exercises

### Exercise 1: House Price Prediction
Build a regression model for:
- House price prediction
- Feature engineering
- Model evaluation
- Cross-validation

### Exercise 2: Customer Segmentation
Create a clustering system for:
- Customer segmentation
- Market basket analysis
- Customer lifetime value
- Churn prediction

### Exercise 3: Text Classification
Develop a text classification system for:
- Spam detection
- Sentiment analysis
- Topic classification
- Language detection

### Exercise 4: Time Series Forecasting
Build a time series model for:
- Stock price prediction
- Sales forecasting
- Weather prediction
- Anomaly detection

---

**Ready to explore more advanced Ruby topics? Let's continue! 🤖**
