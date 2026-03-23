# Machine Learning Examples
# Demonstrating data processing, ML algorithms, and neural networks

puts "=== DATA PROCESSING ==="

require 'csv'

class DataProcessor
  def initialize
    @data = nil
    @headers = []
  end
  
  def load_csv(file_path)
    puts "Loading data from #{file_path}..."
    
    data = []
    headers = []
    
    CSV.foreach(file_path) do |row|
      if headers.empty?
        headers = row.map(&:to_sym)
      else
        # Convert numeric values
        processed_row = headers.zip(row).map do |header, value|
          if value =~ /\A-?\d+\.\d+\z/
            value.to_f
          elsif value =~ /\A-?\d+\z/
            value.to_i
          else
            value
          end
        end.to_h
        
        data << processed_row
      end
    end
    
    @headers = headers
    @data = data
    
    puts "Loaded #{data.length} records with #{headers.length} features"
    @data
  end
  
  def generate_sample_data(n_samples = 100)
    puts "Generating sample dataset with #{n_samples} records..."
    
    @headers = [:id, :feature1, :feature2, :feature3, :target]
    @data = []
    
    n_samples.times do |i|
      # Generate correlated features
      feature1 = rand * 10
      feature2 = feature1 + (rand - 0.5) * 2
      feature3 = (feature1 + feature2) / 2 + (rand - 0.5)
      
      # Generate target based on features
      target = (feature1 + feature2 + feature3) / 3 + (rand - 0.5)
      
      @data << {
        id: i + 1,
        feature1: feature1,
        feature2: feature2,
        feature3: feature3,
        target: target
      }
    end
    
    puts "Generated #{@data.length} records"
    @data
  end
  
  def normalize
    return nil unless @data
    
    puts "Normalizing data..."
    
    normalized_data = @data.map do |row|
      normalized_row = {}
      
      @headers.each do |header|
        next if header == :id  # Don't normalize ID
        
        values = @data.map { |r| r[header] }
        min_val = values.min
        max_val = values.max
        range = max_val - min_val
        
        if range == 0
          normalized_row[header] = 0
        else
          normalized_row[header] = (row[header] - min_val) / range
        end
      end
      
      normalized_row
    end
    
    @data = normalized_data
    puts "Data normalized successfully"
    @data
  end
  
  def standardize
    return nil unless @data
    
    puts "Standardizing data..."
    
    standardized_data = @data.map do |row|
      standardized_row = {}
      
      @headers.each do |header|
        next if header == :id  # Don't standardize ID
        
        values = @data.map { |r| r[header] }
        mean = values.sum.to_f / values.length
        std = Math.sqrt(values.map { |v| (v - mean) ** 2 }.sum / values.length)
        
        if std == 0
          standardized_row[header] = 0
        else
          standardized_row[header] = (row[header] - mean) / std
        end
      end
      
      standardized_row
    end
    
    @data = standardized_data
    puts "Data standardized successfully"
    @data
  end
  
  def split_data(train_ratio = 0.8)
    return nil unless @data
    
    puts "Splitting data (#{train_ratio * 100}% train, #{(1 - train_ratio) * 100}% test)..."
    
    shuffled_data = @data.shuffle
    train_size = (@data.length * train_ratio).to_i
    
    train_data = shuffled_data[0...train_size]
    test_data = shuffled_data[train_size..-1]
    
    puts "Train set: #{train_data.length} records"
    puts "Test set: #{test_data.length} records"
    
    [train_data, test_data]
  end
  
  def feature_selection(target_column = :target)
    return nil unless @data
    
    puts "Selecting features and target..."
    
    features = @headers.reject { |h| h == target_column || h == :id }
    target_data = @data.map { |row| row[target_column] }
    feature_data = @data.map { |row| features.map { |f| row[f] } }
    
    [feature_data, target_data]
  end
  
  def correlation_matrix
    return nil unless @data
    
    puts "Calculating correlation matrix..."
    
    numeric_headers = @headers.reject { |h| h == :id }
    n_features = numeric_headers.length
    
    correlation_matrix = Array.new(n_features) { Array.new(n_features) }
    
    (0...n_features).each do |i|
      (0...n_features).each do |j|
        feature1 = numeric_headers[i]
        feature2 = numeric_headers[j]
        
        values1 = @data.map { |row| row[feature1] }
        values2 = @data.map { |row| row[feature2] }
        
        correlation = calculate_correlation(values1, values2)
        correlation_matrix[i][j] = correlation
      end
    end
    
    puts "Correlation matrix calculated"
    [correlation_matrix, numeric_headers]
  end
  
  def summary_statistics
    return nil unless @data
    
    puts "Calculating summary statistics..."
    
    stats = {}
    
    @headers.each do |header|
      next if header == :id
      
      values = @data.map { |row| row[header] }
      
      stats[header] = {
        count: values.length,
        mean: values.sum.to_f / values.length,
        min: values.min,
        max: values.max,
        std: Math.sqrt(values.map { |v| (v - (values.sum.to_f / values.length)) ** 2 }.sum / values.length)
      }
    end
    
    puts "Summary statistics calculated"
    stats
  end
  
  private
  
  def calculate_correlation(values1, values2)
    n = values1.length
    return 0 if n == 0
    
    mean1 = values1.sum.to_f / n
    mean2 = values2.sum.to_f / n
    
    numerator = (0...n).sum { |i| (values1[i] - mean1) * (values2[i] - mean2) }
    
    sum_sq1 = (0...n).sum { |i| (values1[i] - mean1) ** 2 }
    sum_sq2 = (0...n).sum { |i| (values2[i] - mean2) ** 2 }
    
    denominator = Math.sqrt(sum_sq1 * sum_sq2)
    
    denominator == 0 ? 0 : numerator / denominator
  end
end

puts "Data Processing Example:"

processor = DataProcessor.new

# Generate sample data
data = processor.generate_sample_data(100)

# Show summary statistics
stats = processor.summary_statistics
puts "\nSummary Statistics:"
stats.each do |feature, stat|
  puts "#{feature}: Mean=#{stat[:mean].round(2)}, Min=#{stat[:min].round(2)}, Max=#{stat[:max].round(2)}, Std=#{stat[:std].round(2)}"
end

# Normalize data
processor.normalize

# Split data
train_data, test_data = processor.split_data(0.8)

puts "\n=== LINEAR REGRESSION ==="

class LinearRegression
  def initialize(learning_rate = 0.01, epochs = 1000)
    @learning_rate = learning_rate
    @epochs = epochs
    @weights = nil
    @bias = nil
    @loss_history = []
  end
  
  def fit(X, y)
    puts "Training Linear Regression model..."
    
    n_samples, n_features = X.length, X.first.length
    
    # Initialize weights and bias
    @weights = Array.new(n_features) { rand }
    @bias = 0.0
    
    @epochs.times do |epoch|
      # Forward pass
      predictions = predict_batch(X)
      
      # Calculate gradients
      dw = Array.new(n_features, 0.0)
      db = 0.0
      
      n_samples.times do |i|
        error = predictions[i] - y[i]
        
        n_features.times do |j|
          dw[j] += error * X[i][j]
        end
        
        db += error
      end
      
      # Update weights
      n_features.times do |j|
        @weights[j] -= @learning_rate * dw[j] / n_samples
      end
      @bias -= @learning_rate * db / n_samples
      
      # Calculate and store loss
      loss = calculate_loss(X, y)
      @loss_history << loss
      
      # Print progress
      if epoch % 100 == 0
        puts "Epoch #{epoch}, Loss: #{loss.round(6)}"
      end
    end
    
    puts "Training completed"
  end
  
  def predict(x)
    return nil unless @weights
    
    prediction = @bias
    x.each_with_index do |feature, i|
      prediction += feature * @weights[i]
    end
    
    prediction
  end
  
  def predict_batch(X)
    return [] unless @weights
    
    X.map { |x| predict(x) }
  end
  
  def score(X, y)
    predictions = predict_batch(X)
    
    ss_res = (0...y.length).sum { |i| (y[i] - predictions[i]) ** 2 }
    ss_tot = (0...y.length).sum { |i| (y[i] - y.sum.to_f / y.length) ** 2 }
    
    1 - (ss_res / ss_tot)
  end
  
  def mean_squared_error(X, y)
    predictions = predict_batch(X)
    (0...y.length).sum { |i| (y[i] - predictions[i]) ** 2 } / y.length
  end
  
  def loss_history
    @loss_history
  end
  
  def coefficients
    return nil unless @weights
    
    {
      weights: @weights,
      bias: @bias
    }
  end
end

puts "Linear Regression Example:"

# Prepare data
processor.feature_selection
feature_data, target_data = processor.feature_selection
train_data, test_data = processor.split_data(0.8)

# Extract train/test features and targets
X_train = train_data.map { |row| row.values }
y_train = train_data.map { |row| row[:target] }
X_test = test_data.map { |row| row.values }
y_test = test_data.map { |row| row[:target] }

# Train model
model = LinearRegression.new(0.01, 500)
model.fit(X_train, y_train)

# Evaluate model
train_score = model.score(X_train, y_train)
test_score = model.score(X_test, y_test)
train_mse = model.mean_squared_error(X_train, y_train)
test_mse = model.mean_squared_error(X_test, y_test)

puts "\nModel Performance:"
puts "Train R² Score: #{train_score.round(4)}"
puts "Test R² Score: #{test_score.round(4)}"
puts "Train MSE: #{train_mse.round(4)}"
puts "Test MSE: #{test_mse.round(4)}"

# Show coefficients
coeffs = model.coefficients
puts "\nModel Coefficients:"
puts "Weights: #{coeffs[:weights].map { |w| w.round(4) }.join(', ')}"
puts "Bias: #{coeffs[:bias].round(4)}"

# Make predictions
puts "\nSample Predictions:"
5.times do |i|
  x_sample = X_test[i]
  y_true = y_test[i]
  y_pred = model.predict(x_sample)
  
  puts "Sample #{i + 1}: True=#{y_true.round(2)}, Predicted=#{y_pred.round(2)}, Error=#{(y_true - y_pred).round(2)}"
end

puts "\n=== K-MEANS CLUSTERING ==="

class KMeans
  def initialize(k, max_iterations = 100)
    @k = k
    @max_iterations = max_iterations
    @centroids = []
    @labels = []
    @inertia = 0
  end
  
  def fit(X)
    puts "Training K-Means clustering (k=#{@k})..."
    
    n_samples = X.length
    n_features = X.first.length
    
    # Initialize centroids randomly
    @centroids = initialize_centroids(X)
    
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
      
      if iteration % 10 == 0
        @inertia = calculate_inertia(X)
        puts "Iteration #{iteration}, Inertia: #{@inertia.round(2)}"
      end
    end
    
    @inertia = calculate_inertia(X)
    puts "Training completed. Final inertia: #{@inertia.round(2)}"
  end
  
  def predict(X)
    return [] unless @centroids
    
    X.map { |x| assign_to_cluster(x) }
  end
  
  def cluster_centers
    @centroids
  end
  
  def labels
    @labels
  end
  
  def inertia
    @inertia
  end
  
  def silhouette_score(X)
    return 0 unless @labels && @centroids
    
    n_samples = X.length
    silhouette_scores = []
    
    n_samples.times do |i|
      cluster_i = @labels[i]
      
      # Calculate a(i): average distance to points in same cluster
      same_cluster_indices = @labels.each_index.select { |j, l| l == cluster_i && i != j }.map(&:first)
      
      if same_cluster_indices.length > 0
        a_i = same_cluster_indices.sum do |j|
          euclidean_distance(X[i], X[j])
        end / same_cluster_indices.length
      else
        a_i = 0
      end
      
      # Calculate b(i): minimum average distance to points in other clusters
      b_i = Float::INFINITY
      
      (0...@k).each do |cluster|
        next if cluster == cluster_i
        
        other_cluster_indices = @labels.each_index.select { |j, l| l == cluster }.map(&:first)
        
        if other_cluster_indices.length > 0
          avg_distance = other_cluster_indices.sum do |j|
            euclidean_distance(X[i], X[j])
          end / other_cluster_indices.length
          
          b_i = [b_i, avg_distance].min
        end
      end
      
      # Calculate silhouette score
      if b_i > a_i
        silhouette_score = (b_i - a_i) / [b_i, a_i].max
      else
        silhouette_score = 0
      end
      
      silhouette_scores << silhouette_score
    end
    
    silhouette_scores.sum / silhouette_scores.length
  end
  
  private
  
  def initialize_centroids(X)
    n_samples = X.length
    indices = (0...n_samples).to_a.sample(@k)
    
    indices.map { |i| X[i].dup }
  end
  
  def assign_clusters(X)
    X.map { |x| assign_to_cluster(x) }
  end
  
  def assign_to_cluster(x)
    distances = @centroids.map { |centroid| euclidean_distance(x, centroid) }
    distances.index(distances.min)
  end
  
  def update_centroids(X)
    new_centroids = []
    
    @k.times do |k|
      cluster_indices = @labels.each_index.select { |i, l| l == k }.map(&:first)
      
      if cluster_indices.length > 0
        n_features = X.first.length
        new_centroid = Array.new(n_features, 0.0)
        
        cluster_indices.each do |i|
          n_features.times do |j|
            new_centroid[j] += X[i][j]
          end
        end
        
        n_features.times do |j|
          new_centroid[j] /= cluster_indices.length
        end
        
        new_centroids << new_centroid
      else
        new_centroids << @centroids[k].dup
      end
    end
    
    new_centroids
  end
  
  def converged?(old_centroids, new_centroids)
    total_distance = 0
    
    @k.times do |i|
      total_distance += euclidean_distance(old_centroids[i], new_centroids[i])
    end
    
    total_distance < 1e-6
  end
  
  def calculate_inertia(X)
    total_inertia = 0
    
    X.each_with_index do |x, i|
      cluster = @labels[i]
      centroid = @centroids[cluster]
      total_inertia += euclidean_distance(x, centroid) ** 2
    end
    
    total_inertia
  end
  
  def euclidean_distance(x1, x2)
    Math.sqrt((0...x1.length).sum { |i| (x1[i] - x2[i]) ** 2 })
  end
end

puts "K-Means Clustering Example:"

# Generate sample clustering data
cluster_data = []
cluster_labels = []

# Cluster 1: centered around (2, 2)
30.times do
  cluster_data << [2 + rand * 2, 2 + rand * 2]
  cluster_labels << 0
end

# Cluster 2: centered around (8, 8)
30.times do
  cluster_data << [8 + rand * 2, 8 + rand * 2]
  cluster_labels << 1
end

# Cluster 3: centered around (5, 8)
30.times do
  cluster_data << [5 + rand * 2, 8 + rand * 2]
  cluster_labels << 2
end

# Apply K-means
kmeans = KMeans.new(3, 100)
kmeans.fit(cluster_data)

# Evaluate clustering
predicted_labels = kmeans.predict(cluster_data)
silhouette_score = kmeans.silhouette_score(cluster_data)

puts "\nClustering Results:"
puts "Inertia: #{kmeans.inertia.round(2)}"
puts "Silhouette Score: #{silhouette_score.round(4)}"

# Show cluster centers
puts "\nCluster Centers:"
kmeans.cluster_centers.each_with_index do |center, i|
  puts "Cluster #{i + 1}: (#{center.map { |c| c.round(2) }.join(', ')})"
end

# Show sample assignments
puts "\nSample Assignments:"
10.times do |i|
  true_label = cluster_labels[i]
  predicted_label = predicted_labels[i]
  point = cluster_data[i]
  
  puts "Point #{i + 1}: (#{point.map { |c| c.round(2) }.join(', ')}) -> Cluster #{predicted_label + 1} (True: #{true_label + 1})"
end

puts "\n=== NEURAL NETWORK ==="

class SimpleNeuralNetwork
  def initialize(layer_sizes, learning_rate = 0.01)
    @layer_sizes = layer_sizes
    @learning_rate = learning_rate
    @weights = []
    @biases = []
    @loss_history = []
    
    # Initialize weights and biases
    (layer_sizes.length - 1).times do |i|
      @weights << initialize_weights(layer_sizes[i + 1], layer_sizes[i])
      @biases << Array.new(layer_sizes[i + 1], 0.0)
    end
  end
  
  def train(X, y, epochs = 1000, batch_size = 32)
    puts "Training Neural Network..."
    
    n_samples = X.length
    
    epochs.times do |epoch|
      # Mini-batch training
      (0...n_samples).step(batch_size) do |start|
        batch_end = [start + batch_size, n_samples].min
        X_batch = X[start...batch_end]
        y_batch = y[start...batch_end]
        
        # Forward and backward pass
        forward_backward(X_batch, y_batch)
        
        # Update weights
        update_weights
      end
      
      # Calculate and store loss
      loss = calculate_loss(X, y)
      @loss_history << loss
      
      # Print progress
      if epoch % 100 == 0
        puts "Epoch #{epoch}, Loss: #{loss.round(6)}"
      end
    end
    
    puts "Training completed"
  end
  
  def predict(x)
    return nil unless @weights
    
    activations = [x]
    current_input = x
    
    @weights.each_with_index do |weights, i|
      z = matrix_vector_multiply(weights, current_input)
      z = vector_add(z, @biases[i])
      activation = sigmoid(z)
      activations << activation
      current_input = activation
    end
    
    activations.last
  end
  
  def predict_batch(X)
    return [] unless @weights
    
    X.map { |x| predict(x) }
  end
  
  def score(X, y)
    predictions = predict_batch(X)
    
    # For binary classification
    predicted_classes = predictions.map { |p| p > 0.5 ? 1 : 0 }
    
    correct = (0...y.length).sum { |i| predicted_classes[i] == y[i] }
    correct.to_f / y.length
  end
  
  def loss_history
    @loss_history
  end
  
  private
  
  def initialize_weights(rows, cols)
    Array.new(rows) { Array.new(cols) { rand * 0.1 - 0.05 } }
  end
  
  def forward_backward(X, y)
    # Forward pass
    activations = [X]
    current_input = X
    
    @weights.each_with_index do |weights, i|
      z = matrix_vector_multiply(weights, current_input)
      z = vector_add(z, @biases[i])
      activation = sigmoid(z)
      activations << activation
      current_input = activation
    end
    
    # Backward pass
    output = activations.last
    gradient = (0...output.length).map { |i| 2 * (output[i] - y[i]) / y.length }
    
    @weights.reverse.each_with_index do |weights, i|
      activation = activations[activations.length - i - 1]
      
      # Calculate gradients
      dz = vector_multiply(gradient, sigmoid_derivative(activation))
      
      # Store gradients for weight updates
      instance_variable_set("@dweights_#{i}", matrix_multiply(dz, [activations[activations.length - i - 2]]))
      instance_variable_set("@dbiases_#{i}", dz)
      
      # Calculate gradient for previous layer
      gradient = matrix_vector_multiply(weights.transpose, dz)
    end
  end
  
  def update_weights
    @weights.each_with_index do |weights, i|
      dweights = instance_variable_get("@dweights_#{i}")
      dbiases = instance_variable_get("@dbiases_#{i}")
      
      # Update weights
      weights.each_with_index do |row, j|
        row.each_with_index do |weight, k|
          row[k] -= @learning_rate * dweights[j][k]
        end
      end
      
      # Update biases
      dbiases.each_with_index do |bias, j|
        @biases[i][j] -= @learning_rate * bias
      end
    end
  end
  
  def calculate_loss(X, y)
    predictions = predict_batch(X)
    
    # Mean squared error
    (0...y.length).sum { |i| (predictions[i] - y[i]) ** 2 } / y.length
  end
  
  def sigmoid(z)
    z.map { |v| 1.0 / (1.0 + Math.exp(-v)) }
  end
  
  def sigmoid_derivative(activation)
    activation.map { |a| a * (1 - a) }
  end
  
  def matrix_vector_multiply(matrix, vector)
    matrix.map { |row| row.zip(vector).sum { |a, b| a * b } }
  end
  
  def matrix_multiply(matrix1, matrix2)
    matrix1.map do |row|
      (0...matrix2.first.length).map do |j|
        (0...row.length).sum { |k| row[k] * matrix2[k][j] }
      end
    end
  end
  
  def vector_add(v1, v2)
    v1.zip(v2).map { |a, b| a + b }
  end
  
  def vector_multiply(v1, v2)
    v1.zip(v2).map { |a, b| a * b }
  end
end

puts "Neural Network Example:"

# Generate XOR problem data
X_xor = [
  [0, 0],
  [0, 1],
  [1, 0],
  [1, 1]
]

y_xor = [0, 1, 1, 0]

# Create and train neural network
nn = SimpleNeuralNetwork.new([2, 4, 1], 0.1)
nn.train(X_xor, y_xor, 1000, 4)

# Test network
predictions = nn.predict_batch(X_xor)
accuracy = nn.score(X_xor, y_xor)

puts "\nNeural Network Results:"
puts "Accuracy: #{accuracy.round(4)}"

puts "Predictions:"
X_xor.each_with_index do |x, i|
  predicted = predictions[i].round
  puts "Input: #{x} -> Predicted: #{predicted}, True: #{y_xor[i]}"
end

puts "\n=== MACHINE LEARNING SUMMARY ==="
puts "- Data Processing: CSV loading, normalization, feature selection"
puts "- Linear Regression: Gradient descent, model evaluation, predictions"
puts "- K-Means Clustering: Centroid initialization, convergence, evaluation"
puts "- Neural Network: Forward/backward pass, mini-batch training, classification"
puts "\nAll examples demonstrate fundamental machine learning concepts in Ruby!"
