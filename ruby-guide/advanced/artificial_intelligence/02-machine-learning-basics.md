# Machine Learning Basics in Ruby
# Comprehensive guide to fundamental ML concepts and implementations

## 🎯 Overview

Machine Learning enables computers to learn from data without explicit programming. This guide covers fundamental ML concepts, algorithms, and their implementations in Ruby.

## 🧠 ML Fundamentals

### 1. Machine Learning Concepts

Core ML terminology and concepts:

```ruby
class MachineLearningBasics
  def self.explain_ml_concepts
    puts "Machine Learning Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Supervised Learning",
        description: "Learning from labeled data",
        examples: ["Classification", "Regression", "Prediction"],
        algorithms: ["Linear Regression", "Decision Trees", "Neural Networks"]
      },
      {
        concept: "Unsupervised Learning",
        description: "Learning from unlabeled data",
        examples: ["Clustering", "Dimensionality Reduction", "Anomaly Detection"],
        algorithms: ["K-Means", "PCA", "Autoencoders"]
      },
      {
        concept: "Reinforcement Learning",
        description: "Learning through rewards and punishments",
        examples: ["Game Playing", "Robotics", "Resource Management"],
        algorithms: ["Q-Learning", "Policy Gradients", "Actor-Critic"]
      },
      {
        concept: "Features",
        description: "Input variables used for prediction",
        examples: ["Age", "Income", "Education Level"],
        importance: "Feature selection and engineering"
      },
      {
        concept: "Labels",
        description: "Target variables to predict",
        examples: ["Spam/Not Spam", "House Price", "Customer Churn"],
        types: ["Classification", "Regression", "Multi-class"]
      },
      {
        concept: "Training/Testing Split",
        description: "Separate data for training and evaluation",
        ratio: "Typically 70-80% training, 20-30% testing",
        purpose: "Prevent overfitting and evaluate generalization"
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Examples: #{concept[:examples].join(', ')}" if concept[:examples]
      puts "  Algorithms: #{concept[:algorithms].join(', ')}" if concept[:algorithms]
      puts "  Importance: #{concept[:importance]}" if concept[:importance]
      puts "  Ratio: #{concept[:ratio]}" if concept[:ratio]
      puts "  Purpose: #{concept[:purpose]}" if concept[:purpose]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts
    end
  end
  
  def self.ml_workflow
    puts "\nMachine Learning Workflow:"
    puts "=" * 50
    
    workflow_steps = [
      {
        step: "1. Data Collection",
        description: "Gather relevant data",
        activities: ["Web scraping", "Database queries", "API calls"],
        considerations: ["Data quality", "Quantity", "Relevance"]
      },
      {
        step: "2. Data Preprocessing",
        description: "Clean and prepare data",
        activities: ["Handle missing values", "Normalize features", "Encode categorical data"],
        considerations: ["Data distribution", "Outliers", "Feature scaling"]
      },
      {
        step: "3. Feature Engineering",
        description: "Create meaningful features",
        activities: ["Feature selection", "Feature creation", "Dimensionality reduction"],
        considerations: ["Feature importance", "Correlation", "Domain knowledge"]
      },
      {
        step: "4. Model Selection",
        description: "Choose appropriate algorithm",
        activities: ["Algorithm comparison", "Hyperparameter tuning", "Cross-validation"],
        considerations: ["Problem type", "Data size", "Performance requirements"]
      },
      {
        step: "5. Model Training",
        description: "Train the selected model",
        activities: ["Fit model to data", "Monitor training", "Early stopping"],
        considerations: ["Convergence", "Overfitting", "Computational resources"]
      },
      {
        step: "6. Model Evaluation",
        description: "Assess model performance",
        activities: ["Calculate metrics", "Validate on test set", "Error analysis"],
        considerations: ["Accuracy", "Precision", "Recall", "F1-score"]
      },
      {
        step: "7. Model Deployment",
        description: "Deploy model to production",
        activities: ["API integration", "Monitoring", "Retraining"],
        considerations: ["Scalability", "Latency", "Model drift"]
      }
    ]
    
    workflow_steps.each do |step|
      puts "#{step[:step]}:"
      puts "  Description: #{step[:description]}"
      puts "  Activities: #{step[:activities].join(', ')}"
      puts "  Considerations: #{step[:considerations].join(', ')}"
      puts
    end
  end
  
  # Run ML basics examples
  explain_ml_concepts
  ml_workflow
end
```

### 2. Data Preprocessing

Essential data preparation techniques:

```ruby
class DataPreprocessing
  def self.normalize_features(data)
    puts "Feature Normalization:"
    puts "=" * 40
    
    # Min-Max normalization
    def self.min_max_normalize(features)
      min_val = features.min
      max_val = features.max
      range = max_val - min_val
      
      return features if range == 0
      
      features.map { |value| (value - min_val) / range }
    end
    
    # Z-score normalization
    def self.z_score_normalize(features)
      mean = features.sum.to_f / features.length
      variance = features.sum { |x| (x - mean) ** 2 } / features.length
      std_dev = Math.sqrt(variance)
      
      return features if std_dev == 0
      
      features.map { |value| (value - mean) / std_dev }
    end
    
    # Example data
    features = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100]
    
    puts "Original features: #{features}"
    puts "Min-Max normalized: #{min_max_normalize(features).map { |x| x.round(3) }}"
    puts "Z-score normalized: #{z_score_normalize(features).map { |x| x.round(3) }}"
  end
  
  def self.handle_missing_values
    puts "\nMissing Value Handling:"
    puts "=" * 40
    
    data = [1, 2, nil, 4, 5, nil, 7, 8, 9, nil]
    
    puts "Original data: #{data}"
    
    # Remove missing values
    complete_data = data.compact
    puts "After removing missing: #{complete_data}"
    
    # Fill with mean
    mean = complete_data.sum.to_f / complete_data.length
    mean_filled = data.map { |x| x.nil? ? mean : x }
    puts "Filled with mean (#{mean.round(2)}): #{mean_filled.map { |x| x.round(2) }}"
    
    # Fill with median
    sorted = complete_data.sort
    median = sorted.length.odd? ? sorted[sorted.length / 2] : (sorted[sorted.length / 2 - 1] + sorted[sorted.length / 2]) / 2.0
    median_filled = data.map { |x| x.nil? ? median : x }
    puts "Filled with median (#{median}): #{median_filled}"
    
    # Fill with mode
    frequency = complete_data.each_with_object(Hash.new(0)) { |x, h| h[x] += 1 }
    mode = frequency.max_by { |_, count| count }[0]
    mode_filled = data.map { |x| x.nil? ? mode : x }
    puts "Filled with mode (#{mode}): #{mode_filled}"
  end
  
  def self.encode_categorical_data
    puts "\nCategorical Data Encoding:"
    puts "=" * 40
    
    categories = ['Red', 'Blue', 'Green', 'Red', 'Green', 'Blue', 'Yellow']
    
    puts "Original categories: #{categories}"
    
    # Label encoding
    unique_categories = categories.uniq
    label_map = unique_categories.each_with_index.to_h
    label_encoded = categories.map { |cat| label_map[cat] }
    
    puts "Label encoding: #{label_map}"
    puts "Encoded values: #{label_encoded}"
    
    # One-hot encoding
    one_hot_encoded = categories.map do |cat|
      unique_categories.map { |unique_cat| cat == unique_cat ? 1 : 0 }
    end
    
    puts "One-hot encoding:"
    categories.each_with_index do |cat, i|
      puts "  #{cat}: #{one_hot_encoded[i]}"
    end
  end
  
  def self.feature_scaling
    puts "\nFeature Scaling:"
    puts "=" * 40
    
    # Multiple features with different scales
    features = {
      age: [25, 30, 35, 40, 45, 50],
      income: [30000, 45000, 60000, 75000, 90000, 105000],
      score: [85, 90, 78, 92, 88, 95]
    }
    
    puts "Original features:"
    features.each do |name, values|
      puts "  #{name}: #{values}"
    end
    
    # Normalize each feature
    normalized_features = {}
    features.each do |name, values|
      min_val = values.min
      max_val = values.max
      range = max_val - min_val
      
      normalized_features[name] = values.map do |value|
        range == 0 ? 0 : (value - min_val) / range
      end
    end
    
    puts "\nNormalized features:"
    normalized_features.each do |name, values|
      puts "  #{name}: #{values.map { |x| x.round(3) }}"
    end
  end
  
  # Run preprocessing examples
  normalize_features(nil)
  handle_missing_values
  encode_categorical_data
  feature_scaling
end
```

## 📈 Supervised Learning

### 3. Linear Regression

Simple linear regression implementation:

```ruby
class LinearRegression
  def initialize(learning_rate = 0.01, epochs = 1000)
    @learning_rate = learning_rate
    @epochs = epochs
    @weights = nil
    @bias = nil
  end
  
  def fit(X, y)
    n_samples, n_features = X.length, X[0].length
    
    # Initialize weights and bias
    @weights = Array.new(n_features) { rand }
    @bias = 0
    
    @epochs.times do |epoch|
      # Make predictions
      predictions = X.map { |sample| predict_single(sample) }
      
      # Calculate gradients
      dw = Array.new(n_features, 0)
      db = 0
      
      n_samples.times do |i|
        error = predictions[i] - y[i]
        
        n_features.times do |j|
          dw[j] += error * X[i][j]
        end
        
        db += error
      end
      
      # Update weights and bias
      n_features.times do |j|
        @weights[j] -= @learning_rate * dw[j] / n_samples
      end
      
      @bias -= @learning_rate * db / n_samples
      
      # Print progress
      if epoch % 100 == 0
        mse = calculate_mse(X, y)
        puts "Epoch #{epoch}: MSE = #{mse.round(4)}"
      end
    end
  end
  
  def predict_single(x)
    prediction = @bias
    x.each_with_index do |feature, j|
      prediction += @weights[j] * feature
    end
    prediction
  end
  
  def predict(X)
    X.map { |sample| predict_single(sample) }
  end
  
  def calculate_mse(X, y)
    predictions = predict(X)
    errors = predictions.zip(y).map { |pred, actual| (pred - actual) ** 2 }
    errors.sum / errors.length
  end
  
  def r_squared(X, y)
    predictions = predict(X)
    y_mean = y.sum.to_f / y.length
    
    ss_tot = y.sum { |actual| (actual - y_mean) ** 2 }
    ss_res = predictions.zip(y).sum { |pred, actual| (pred - actual) ** 2 }
    
    1 - (ss_res / ss_tot)
  end
  
  def self.demonstrate_linear_regression
    puts "Linear Regression Demonstration:"
    puts "=" * 50
    
    # Generate sample data
    X = (1..100).map { |i| [i.to_f] }
    y = X.map { |features| 2 * features[0] + 1 + rand * 10 } # y = 2x + 1 + noise
    
    puts "Generated #{X.length} data points"
    puts "Sample: X[0] = #{X[0]}, y[0] = #{y[0].round(2)}"
    
    # Split data
    split_index = (X.length * 0.8).to_i
    X_train, X_test = X[0...split_index], X[split_index..-1]
    y_train, y_test = y[0...split_index], y[split_index..-1]
    
    puts "\nTraining set: #{X_train.length} samples"
    puts "Test set: #{X_test.length} samples"
    
    # Train model
    model = LinearRegression.new(0.0001, 500)
    puts "\nTraining model..."
    model.fit(X_train, y_train)
    
    # Evaluate model
    train_mse = model.calculate_mse(X_train, y_train)
    test_mse = model.calculate_mse(X_test, y_test)
    train_r2 = model.r_squared(X_train, y_train)
    test_r2 = model.r_squared(X_test, y_test)
    
    puts "\nModel Performance:"
    puts "Training MSE: #{train_mse.round(4)}"
    puts "Test MSE: #{test_mse.round(4)}"
    puts "Training R²: #{train_r2.round(4)}"
    puts "Test R²: #{test_r2.round(4)}"
    
    # Make predictions
    sample_x = [[50], [75], [100]]
    predictions = model.predict(sample_x)
    
    puts "\nPredictions:"
    sample_x.each_with_index do |x, i|
      puts "X = #{x[0]} -> y = #{predictions[i].round(2)}"
    end
    
    puts "\nLearned parameters:"
    puts "Weight: #{model.weights[0].round(4)}"
    puts "Bias: #{model.bias.round(4)}"
  end
end
```

### 4. Logistic Regression

Binary classification implementation:

```ruby
class LogisticRegression
  def initialize(learning_rate = 0.01, epochs = 1000)
    @learning_rate = learning_rate
    @epochs = epochs
    @weights = nil
    @bias = nil
  end
  
  def sigmoid(z)
    1.0 / (1.0 + Math.exp(-z))
  end
  
  def fit(X, y)
    n_samples, n_features = X.length, X[0].length
    
    # Initialize weights and bias
    @weights = Array.new(n_features) { rand * 0.01 }
    @bias = 0
    
    @epochs.times do |epoch|
      # Make predictions
      predictions = X.map { |sample| predict_probability_single(sample) }
      
      # Calculate gradients
      dw = Array.new(n_features, 0)
      db = 0
      
      n_samples.times do |i|
        error = predictions[i] - y[i]
        
        n_features.times do |j|
          dw[j] += error * X[i][j]
        end
        
        db += error
      end
      
      # Update weights and bias
      n_features.times do |j|
        @weights[j] -= @learning_rate * dw[j] / n_samples
      end
      
      @bias -= @learning_rate * db / n_samples
      
      # Print progress
      if epoch % 100 == 0
        loss = calculate_loss(X, y)
        accuracy = calculate_accuracy(X, y)
        puts "Epoch #{epoch}: Loss = #{loss.round(4)}, Accuracy = #{accuracy.round(4)}"
      end
    end
  end
  
  def predict_probability_single(x)
    z = @bias
    x.each_with_index do |feature, j|
      z += @weights[j] * feature
    end
    sigmoid(z)
  end
  
  def predict_single(x)
    probability = predict_probability_single(x)
    probability >= 0.5 ? 1 : 0
  end
  
  def predict(X)
    X.map { |sample| predict_single(sample) }
  end
  
  def predict_probability(X)
    X.map { |sample| predict_probability_single(sample) }
  end
  
  def calculate_loss(X, y)
    predictions = predict_probability(X)
    
    loss = 0
    predictions.each_with_index do |pred, i|
      # Avoid log(0)
      pred = [pred, 1e-15].max
      pred = [pred, 1 - 1e-15].min
      
      loss += y[i] * Math.log(pred) + (1 - y[i]) * Math.log(1 - pred)
    end
    
    -loss / predictions.length
  end
  
  def calculate_accuracy(X, y)
    predictions = predict(X)
    correct = predictions.zip(y).count { |pred, actual| pred == actual }
    correct.to_f / predictions.length
  end
  
  def self.demonstrate_logistic_regression
    puts "Logistic Regression Demonstration:"
    puts "=" * 50
    
    # Generate sample data (two classes)
    # Class 0: points around (2, 2)
    # Class 1: points around (8, 8)
    class_0 = 50.times.map { [2 + rand * 2, 2 + rand * 2] }
    class_1 = 50.times.map { [8 + rand * 2, 8 + rand * 2] }
    
    X = class_0 + class_1
    y = [0] * 50 + [1] * 50
    
    puts "Generated #{X.length} data points"
    puts "Class 0: 50 points around (2, 2)"
    puts "Class 1: 50 points around (8, 8)"
    
    # Split data
    split_index = (X.length * 0.8).to_i
    X_train, X_test = X[0...split_index], X[split_index..-1]
    y_train, y_test = y[0...split_index], y[split_index..-1]
    
    puts "\nTraining set: #{X_train.length} samples"
    puts "Test set: #{X_test.length} samples"
    
    # Train model
    model = LogisticRegression.new(0.1, 1000)
    puts "\nTraining model..."
    model.fit(X_train, y_train)
    
    # Evaluate model
    train_accuracy = model.calculate_accuracy(X_train, y_train)
    test_accuracy = model.calculate_accuracy(X_test, y_test)
    train_loss = model.calculate_loss(X_train, y_train)
    test_loss = model.calculate_loss(X_test, y_test)
    
    puts "\nModel Performance:"
    puts "Training Accuracy: #{train_accuracy.round(4)}"
    puts "Test Accuracy: #{test_accuracy.round(4)}"
    puts "Training Loss: #{train_loss.round(4)}"
    puts "Test Loss: #{test_loss.round(4)}"
    
    # Make predictions
    test_points = [[1, 1], [5, 5], [10, 10]]
    predictions = model.predict(test_points)
    probabilities = model.predict_probability(test_points)
    
    puts "\nPredictions:"
    test_points.each_with_index do |point, i|
      puts "Point #{point} -> Class #{predictions[i]} (prob: #{probabilities[i].round(4)})"
    end
    
    puts "\nLearned parameters:"
    puts "Weights: #{model.weights.map { |w| w.round(4) }}"
    puts "Bias: #{model.bias.round(4)}"
  end
end
```

## 🎯 Unsupervised Learning

### 5. K-Means Clustering

Clustering algorithm implementation:

```ruby
class KMeans
  def initialize(k = 3, max_iterations = 100)
    @k = k
    @max_iterations = max_iterations
    @centroids = nil
    @labels = nil
  end
  
  def fit(X)
    n_samples = X.length
    n_features = X[0].length
    
    # Initialize centroids randomly
    @centroids = Array.new(@k) do
      centroid = Array.new(n_features) do |j|
        min_val = X.map { |sample| sample[j] }.min
        max_val = X.map { |sample| sample[j] }.max
        min_val + rand * (max_val - min_val)
      end
      centroid
    end
    
    @max_iterations.times do |iteration|
      # Assign samples to closest centroid
      @labels = X.map do |sample|
        distances = @centroids.map { |centroid| euclidean_distance(sample, centroid) }
        distances.index(distances.min)
      end
      
      # Update centroids
      new_centroids = Array.new(@k) do |cluster_idx|
        cluster_samples = X.each_with_index.select { |_, i| @labels[i] == cluster_idx }.map(&:first)
        
        if cluster_samples.empty?
          @centroids[cluster_idx] # Keep old centroid if no samples
        else
          n_features.times.map do |j|
            cluster_samples.map { |sample| sample[j] }.sum / cluster_samples.length
          end
        end
      end
      
      # Check for convergence
      if converged?(@centroids, new_centroids)
        puts "Converged after #{iteration + 1} iterations"
        break
      end
      
      @centroids = new_centroids
    end
  end
  
  def predict(X)
    X.map do |sample|
      distances = @centroids.map { |centroid| euclidean_distance(sample, centroid) }
      distances.index(distances.min)
    end
  end
  
  def euclidean_distance(point1, point2)
    Math.sqrt(point1.zip(point2).sum { |a, b| (a - b) ** 2 })
  end
  
  def converged?(old_centroids, new_centroids)
    old_centroids.zip(new_centroids).all? do |old, new|
      euclidean_distance(old, new) < 1e-6
    end
  end
  
  def inertia(X)
    total_distance = 0
    
    X.each_with_index do |sample, i|
      centroid = @centroids[@labels[i]]
      total_distance += euclidean_distance(sample, centroid) ** 2
    end
    
    total_distance
  end
  
  def self.demonstrate_kmeans
    puts "K-Means Clustering Demonstration:"
    puts "=" * 50
    
    # Generate sample data (3 clusters)
    cluster1 = 30.times.map { [2 + rand * 2, 2 + rand * 2] }
    cluster2 = 30.times.map { [8 + rand * 2, 2 + rand * 2] }
    cluster3 = 30.times.map { [5 + rand * 2, 8 + rand * 2] }
    
    X = cluster1 + cluster2 + cluster3
    
    puts "Generated #{X.length} data points in 3 clusters"
    puts "Cluster 1: 30 points around (2, 2)"
    puts "Cluster 2: 30 points around (8, 2)"
    puts "Cluster 3: 30 points around (5, 8)"
    
    # Apply K-Means
    kmeans = KMeans.new(3, 100)
    puts "\nApplying K-Means clustering..."
    kmeans.fit(X)
    
    # Evaluate clustering
    inertia = kmeans.inertia(X)
    puts "\nClustering Results:"
    puts "Inertia: #{inertia.round(4)}"
    
    # Count samples in each cluster
    cluster_counts = Array.new(3, 0)
    kmeans.labels.each { |label| cluster_counts[label] += 1 }
    
    puts "Cluster sizes: #{cluster_counts}"
    
    # Show centroids
    puts "\nFinal centroids:"
    kmeans.centroids.each_with_index do |centroid, i|
      puts "Cluster #{i}: #{centroid.map { |c| c.round(2) }}"
    end
    
    # Visualize clustering (simplified)
    puts "\nSample assignments:"
    10.times do |i|
      puts "Point #{X[i]} -> Cluster #{kmeans.labels[i]}"
    end
  end
end
```

### 6. Principal Component Analysis

Dimensionality reduction technique:

```ruby
class PCA
  def initialize(n_components = 2)
    @n_components = n_components
    @components = nil
    @mean = nil
  end
  
  def fit(X)
    n_samples, n_features = X.length, X[0].length
    
    # Center the data
    @mean = n_features.times.map { |j| X.map { |sample| sample[j] }.sum / n_samples }
    X_centered = X.map do |sample|
      sample.each_with_index.map { |value, j| value - @mean[j] }
    end
    
    # Calculate covariance matrix
    covariance_matrix = calculate_covariance_matrix(X_centered)
    
    # Calculate eigenvalues and eigenvectors
    eigenvalues, eigenvectors = calculate_eigenvectors(covariance_matrix)
    
    # Sort eigenvectors by eigenvalues (descending)
    sorted_indices = eigenvalues.each_with_index.sort_by { |val, _| -val }.map(&:last)
    @components = sorted_indices.first(@n_components).map { |i| eigenvectors[i] }
  end
  
  def transform(X)
    X.map do |sample|
      @components.map do |component|
        sample.each_with_index.sum { |value, j| value * component[j] }
      end
    end
  end
  
  def fit_transform(X)
    fit(X)
    transform(X)
  end
  
  def explained_variance_ratio(X)
    n_samples, n_features = X.length, X[0].length
    
    # Center the data
    X_centered = X.map do |sample|
      sample.each_with_index.map { |value, j| value - @mean[j] }
    end
    
    # Calculate total variance
    total_variance = n_features.times.sum do |j|
      mean = @mean[j]
      X_centered.sum { |sample| (sample[j]) ** 2 } / (n_samples - 1)
    end
    
    # Calculate explained variance by each component
    explained_variances = @components.map do |component|
      projected = X_centered.map do |sample|
        sample.each_with_index.sum { |value, j| value * component[j] }
      end
      projected.sum { |val| val ** 2 } / (n_samples - 1)
    end
    
    explained_variances.map { |var| var / total_variance }
  end
  
  private
  
  def calculate_covariance_matrix(X)
    n_samples, n_features = X.length, X[0].length
    
    # Transpose for easier calculation
    X_transposed = n_features.times.map { |j| X.map { |sample| sample[j] } }
    
    # Calculate covariance matrix
    covariance_matrix = Array.new(n_features) do |i|
      Array.new(n_features) do |j|
        mean_i = X_transposed[i].sum / n_samples
        mean_j = X_transposed[j].sum / n_samples
        
        covariance = X_transposed[i].zip(X_transposed[j]).sum do |x_i, x_j|
          (x_i - mean_i) * (x_j - mean_j)
        end / (n_samples - 1)
        
        covariance
      end
    end
    
    covariance_matrix
  end
  
  def calculate_eigenvectors(matrix)
    # Simplified eigenvalue calculation (power iteration)
    n = matrix.length
    eigenvalues = []
    eigenvectors = []
    
    n.times do |i|
      # Power iteration to find dominant eigenvalue and eigenvector
      vector = Array.new(n) { rand }
      
      100.times do
        new_vector = Array.new(n) do |j|
          matrix[j].zip(vector).sum { |val, vec| val * vec }
        end
        
        norm = Math.sqrt(new_vector.sum { |v| v ** 2 })
        vector = new_vector.map { |v| v / norm }
      end
      
      # Calculate eigenvalue
      eigenvalue = 0
      n.times do |j|
        eigenvalue += matrix[j].zip(vector).sum { |val, vec| val * vec } * vector[j]
      end
      
      eigenvalues << eigenvalue
      eigenvectors << vector
      
      # Deflate matrix (simplified)
      n.times do |j|
        n.times do |k|
          matrix[j][k] -= eigenvalue * vector[j] * vector[k]
        end
      end
    end
    
    [eigenvalues, eigenvectors]
  end
  
  def self.demonstrate_pca
    puts "Principal Component Analysis Demonstration:"
    puts "=" * 50
    
    # Generate sample data (3D points)
    X = 100.times.map do
      x = rand * 10
      y = 2 * x + rand * 2
      z = 3 * x + rand * 3
      [x, y, z]
    end
    
    puts "Generated #{X.length} 3D data points"
    puts "Sample: #{X[0].map { |v| v.round(2) }}"
    
    # Apply PCA
    pca = PCA.new(2)
    puts "\nApplying PCA to reduce to 2 dimensions..."
    X_transformed = pca.fit_transform(X)
    
    # Show explained variance
    explained_variance = pca.explained_variance_ratio(X)
    puts "\nExplained variance ratio: #{explained_variance.map { |v| v.round(4) }}"
    puts "Total explained variance: #{explained_variance.sum.round(4)}"
    
    # Show transformed data
    puts "\nTransformed data (first 5 points):"
    X_transformed.first(5).each_with_index do |point, i|
      original = X[i].map { |v| v.round(2) }
      transformed = point.map { |v| v.round(2) }
      puts "Original: #{original} -> Transformed: #{transformed}"
    end
    
    # Show principal components
    puts "\nPrincipal components:"
    pca.components.each_with_index do |component, i|
      puts "PC #{i + 1}: #{component.map { |c| c.round(4) }}"
    end
  end
end
```

## 📊 Model Evaluation

### 7. Evaluation Metrics

Comprehensive model evaluation:

```ruby
class ModelEvaluation
  def self.classification_metrics(y_true, y_pred)
    puts "Classification Metrics:"
    puts "=" * 40
    
    # Calculate confusion matrix
    tp, tn, fp, fn = 0, 0, 0, 0
    
    y_true.zip(y_pred).each do |true_label, pred_label|
      case [true_label, pred_label]
      when [1, 1]
        tp += 1
      when [0, 0]
        tn += 1
      when [0, 1]
        fp += 1
      when [1, 0]
        fn += 1
      end
    end
    
    puts "Confusion Matrix:"
    puts "  Predicted 0  Predicted 1"
    puts "Actual 0    #{tn}        #{fp}"
    puts "Actual 1    #{fn}        #{tp}"
    
    # Calculate metrics
    accuracy = (tp + tn).to_f / (tp + tn + fp + fn)
    precision = tp.to_f / (tp + fp) if (tp + fp) > 0
    recall = tp.to_f / (tp + fn) if (tp + fn) > 0
    f1_score = 2 * precision * recall / (precision + recall) if (precision + recall) > 0
    specificity = tn.to_f / (tn + fp) if (tn + fp) > 0
    
    puts "\nMetrics:"
    puts "Accuracy: #{accuracy.round(4)}"
    puts "Precision: #{precision.round(4)}"
    puts "Recall: #{recall.round(4)}"
    puts "F1-Score: #{f1_score.round(4)}"
    puts "Specificity: #{specificity.round(4)}"
    
    {
      accuracy: accuracy,
      precision: precision,
      recall: recall,
      f1_score: f1_score,
      specificity: specificity,
      confusion_matrix: { tp: tp, tn: tn, fp: fp, fn: fn }
    }
  end
  
  def self.regression_metrics(y_true, y_pred)
    puts "\nRegression Metrics:"
    puts "=" * 40
    
    n = y_true.length
    
    # Calculate metrics
    mse = y_true.zip(y_pred).sum { |true_val, pred_val| (true_val - pred_val) ** 2 } / n
    rmse = Math.sqrt(mse)
    mae = y_true.zip(y_pred).sum { |true_val, pred_val| (true_val - pred_val).abs } / n
    
    y_mean = y_true.sum / n
    ss_tot = y_true.sum { |val| (val - y_mean) ** 2 }
    ss_res = y_true.zip(y_pred).sum { |true_val, pred_val| (true_val - pred_val) ** 2 }
    
    r_squared = 1 - (ss_res / ss_tot)
    adjusted_r_squared = 1 - ((1 - r_squared) * (n - 1) / (n - 2)) if n > 2
    
    puts "Mean Squared Error (MSE): #{mse.round(4)}"
    puts "Root Mean Squared Error (RMSE): #{rmse.round(4)}"
    puts "Mean Absolute Error (MAE): #{mae.round(4)}"
    puts "R-squared: #{r_squared.round(4)}"
    puts "Adjusted R-squared: #{adjusted_r_squared.round(4)}"
    
    {
      mse: mse,
      rmse: rmse,
      mae: mae,
      r_squared: r_squared,
      adjusted_r_squared: adjusted_r_squared
    }
  end
  
  def self.cross_validation(model_class, X, y, k = 5)
    puts "\n#{k}-Fold Cross Validation:"
    puts "=" * 40
    
    n_samples = X.length
    fold_size = n_samples / k
    
    scores = []
    
    k.times do |fold|
      # Split data
      test_start = fold * fold_size
      test_end = (fold + 1) * fold_size - 1
      
      X_test = X[test_start..test_end]
      y_test = y[test_start..test_end]
      
      X_train = X[0...test_start] + X[test_end + 1..-1]
      y_train = y[0...test_start] + y[test_end + 1..-1]
      
      # Train and evaluate
      model = model_class.new
      model.fit(X_train, y_train)
      
      if model_class == LinearRegression
        score = model.r_squared(X_test, y_test)
      elsif model_class == LogisticRegression
        score = model.calculate_accuracy(X_test, y_test)
      end
      
      scores << score
      puts "Fold #{fold + 1}: Score = #{score.round(4)}"
    end
    
    mean_score = scores.sum / scores.length
    std_score = Math.sqrt(scores.sum { |score| (score - mean_score) ** 2 } / scores.length)
    
    puts "\nMean Score: #{mean_score.round(4)}"
    puts "Standard Deviation: #{std_score.round(4)}"
    
    scores
  end
  
  def self.demonstrate_evaluation
    puts "Model Evaluation Demonstration:"
    puts "=" * 50
    
    # Classification evaluation
    y_true_class = [0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0]
    y_pred_class = [0, 1, 0, 1, 0, 0, 0, 1, 1, 1, 0, 1, 0, 0, 1]
    
    classification_metrics(y_true_class, y_pred_class)
    
    # Regression evaluation
    y_true_reg = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
    y_pred_reg = [1.1, 2.2, 2.8, 4.1, 4.9, 6.2, 6.8, 8.1, 9.2, 9.8]
    
    regression_metrics(y_true_reg, y_pred_reg)
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Data Preprocessing**: Implement normalization and encoding
2. **Linear Regression**: Build simple regression model
3. **Logistic Regression**: Implement binary classifier
4. **K-Means**: Implement clustering algorithm

### Intermediate Exercises

1. **PCA**: Implement dimensionality reduction
2. **Model Evaluation**: Calculate various metrics
3. **Cross Validation**: Implement K-fold CV
4. **Feature Engineering**: Create meaningful features

### Advanced Exercises

1. **Neural Networks**: Implement simple neural network
2. **Ensemble Methods**: Build ensemble models
3. **Hyperparameter Tuning**: Optimize model parameters
4. **Real Applications**: Apply ML to real problems

---

## 🎯 Summary

Machine Learning Basics in Ruby provide:

- **ML Fundamentals** - Core concepts and workflow
- **Data Preprocessing** - Cleaning and preparing data
- **Supervised Learning** - Linear and logistic regression
- **Unsupervised Learning** - K-means and PCA
- **Model Evaluation** - Comprehensive metrics
- **Practical Implementations** - Working Ruby ML code

Master these fundamentals to build intelligent Ruby applications!
