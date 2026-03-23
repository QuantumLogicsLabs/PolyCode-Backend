# AI and Machine Learning Examples in Ruby
# Demonstrating AI/ML concepts and implementations

require 'matrix'
require 'json'
require 'set'

class AIMachineLearningExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "🤖 AI and Machine Learning Examples in Ruby"
    puts "=========================================="
    puts "Explore AI/ML concepts and implementations!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 AI/ML Examples Menu:"
      puts "1. Neural Networks"
      puts "2. Decision Trees"
      puts "3. K-Means Clustering"
      puts "4. Linear Regression"
      puts "5. Natural Language Processing"
      puts "6. Genetic Algorithms"
      puts "7. Reinforcement Learning"
      puts "8. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-8): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        neural_networks
      when 2
        decision_trees
      when 3
        kmeans_clustering
      when 4
        linear_regression
      when 5
        nlp_examples
      when 6
        genetic_algorithms
      when 7
        reinforcement_learning
      when 8
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def neural_networks
    puts "\n🧠 Example 1: Neural Networks"
    puts "=" * 50
    puts "Implementing simple neural networks from scratch."
    puts ""
    
    # Neural network implementation
    puts "🧠 Simple Neural Network:"
    
    class NeuralNetwork
      def initialize(input_size, hidden_size, output_size)
        @input_size = input_size
        @hidden_size = hidden_size
        @output_size = output_size
        
        # Initialize weights with random values
        @weights_ih = Matrix.build(hidden_size, input_size) { rand(-0.5..0.5) }
        @weights_ho = Matrix.build(output_size, hidden_size) { rand(-0.5..0.5) }
        
        @bias_h = Matrix.build(hidden_size, 1) { rand(-0.5..0.5) }
        @bias_o = Matrix.build(output_size, 1) { rand(-0.5..0.5) }
        
        @learning_rate = 0.1
      end
      
      def sigmoid(x)
        1.0 / (1.0 + Math.exp(-x))
      end
      
      def sigmoid_derivative(x)
        x * (1.0 - x)
      end
      
      def forward(input_array)
        # Convert input to matrix
        inputs = Matrix.column_vector(input_array)
        
        # Calculate hidden layer
        hidden = @weights_ih * inputs + @bias_h
        hidden = hidden.map { |x| sigmoid(x) }
        
        # Calculate output layer
        output = @weights_ho * hidden + @bias_o
        output = output.map { |x| sigmoid(x) }
        
        [inputs, hidden, output]
      end
      
      def train(input_array, target_array)
        # Forward pass
        inputs, hidden, outputs = forward(input_array)
        
        # Convert targets to matrix
        targets = Matrix.column_vector(target_array)
        
        # Calculate output errors
        output_errors = targets - outputs
        output_gradients = outputs.map { |x| sigmoid_derivative(x) }
        output_gradients = output_gradients.elementwise_product(output_errors)
        output_gradients *= @learning_rate
        
        # Calculate hidden-to-output weights deltas
        weights_ho_deltas = output_gradients * hidden.transpose
        
        # Adjust hidden-to-output weights
        @weights_ho += weights_ho_deltas
        @bias_o += output_gradients
        
        # Calculate hidden errors
        hidden_errors = @weights_ho.transpose * output_errors
        hidden_gradients = hidden.map { |x| sigmoid_derivative(x) }
        hidden_gradients = hidden_gradients.elementwise_product(hidden_errors)
        hidden_gradients *= @learning_rate
        
        # Calculate input-to-hidden weights deltas
        weights_ih_deltas = hidden_gradients * inputs.transpose
        
        # Adjust input-to-hidden weights
        @weights_ih += weights_ih_deltas
        @bias_h += hidden_gradients
      end
      
      def predict(input_array)
        _, _, outputs = forward(input_array)
        outputs.to_a.flatten
      end
      
      def save_weights(filename)
        data = {
          weights_ih: @weights_ih.to_a,
          weights_ho: @weights_ho.to_a,
          bias_h: @bias_h.to_a,
          bias_o: @bias_o.to_a
        }
        File.write(filename, data.to_json)
      end
      
      def load_weights(filename)
        data = JSON.parse(File.read(filename))
        @weights_ih = Matrix.rows(data['weights_ih'])
        @weights_ho = Matrix.rows(data['weights_ho'])
        @bias_h = Matrix.rows(data['bias_h'])
        @bias_o = Matrix.rows(data['bias_o'])
      end
    end
  
    # XOR problem demonstration
    puts "\nXOR Problem Training:"
    
    nn = NeuralNetwork.new(2, 4, 1)
    
    # Training data for XOR
    training_data = [
      [[0, 0], [0]],
      [[0, 1], [1]],
      [[1, 0], [1]],
      [[1, 1], [0]]
    ]
    
    # Train the network
    puts "Training neural network..."
    10000.times do |epoch|
      training_data.each do |input, target|
        nn.train(input, target)
      end
      
      if epoch % 1000 == 0
        puts "Epoch #{epoch}: Training..."
      end
    end
    
    # Test the network
    puts "\nTesting neural network:"
    test_data = [
      [0, 0],
      [0, 1],
      [1, 0],
      [1, 1]
    ]
    
    test_data.each do |input|
      prediction = nn.predict(input)
      puts "  Input: #{input} -> Output: #{prediction[0].round(4)} (Expected: #{input[0] ^ input[1]})"
    end
    
    # Iris classification example
    puts "\nIris Classification Example:"
    
    # Simplified iris dataset (petal length, petal width)
    iris_data = [
      [[1.4, 0.2], [1, 0, 0]],  # Setosa
      [[1.5, 0.2], [1, 0, 0]],  # Setosa
      [[4.1, 1.3], [0, 1, 0]],  # Versicolor
      [[3.9, 1.4], [0, 1, 0]],  # Versicolor
      [[5.5, 1.8], [0, 0, 1]],  # Virginica
      [[5.7, 1.9], [0, 0, 1]]   # Virginica
    ]
    
    iris_nn = NeuralNetwork.new(2, 6, 3)
    
    puts "Training iris classifier..."
    5000.times do |epoch|
      iris_data.each do |input, target|
        iris_nn.train(input, target)
      end
      
      if epoch % 1000 == 0
        puts "Epoch #{epoch}: Training..."
      end
    end
    
    # Test iris classifier
    test_iris = [
      [[1.3, 0.2], "Setosa"],
      [[4.0, 1.3], "Versicolor"],
      [[5.6, 1.8], "Virginica"]
    ]
    
    test_iris.each do |input, species|
      prediction = iris_nn.predict(input)
      predicted_class = prediction.index(prediction.max)
      class_names = ["Setosa", "Versicolor", "Virginica"]
      puts "  Input: #{input} -> Predicted: #{class_names[predicted_class]} (Actual: #{species})"
    end
    
    @examples << {
      title: "Neural Networks",
      description: "Simple neural network implementation with backpropagation",
      code: <<~RUBY
        class NeuralNetwork
          def initialize(input_size, hidden_size, output_size)
            @weights_ih = Matrix.build(hidden_size, input_size) { rand(-0.5..0.5) }
            @weights_ho = Matrix.build(output_size, hidden_size) { rand(-0.5..0.5) }
          end
          
          def forward(input_array)
            inputs = Matrix.column_vector(input_array)
            hidden = sigmoid(@weights_ih * inputs + @bias_h)
            output = sigmoid(@weights_ho * hidden + @bias_o)
            [inputs, hidden, output]
          end
        end
      RUBY
    }
    
    puts "\n✅ Neural Networks example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def decision_trees
    puts "\n🌳 Example 2: Decision Trees"
    puts "=" * 50
    puts "Implementing decision tree algorithms."
    puts ""
    
    # Decision tree implementation
    puts "🌳 Decision Tree Implementation:"
    
    class DecisionTree
      def initialize(max_depth = 3)
        @max_depth = max_depth
        @root = nil
      end
      
      def fit(data, labels)
        @root = build_tree(data, labels, 0)
      end
      
      def predict(sample)
        traverse_tree(@root, sample)
      end
      
      private
      
      def build_tree(data, labels, depth)
        # Stop conditions
        return create_leaf(labels) if depth >= @max_depth
        return create_leaf(labels) if all_same_labels?(labels)
        return create_leaf(labels) if data.empty?
        
        # Find best split
        best_feature, best_threshold = find_best_split(data, labels)
        
        return create_leaf(labels) unless best_feature
        
        # Split data
        left_indices, right_indices = split_data(data, best_feature, best_threshold)
        
        # Create node
        node = {
          feature: best_feature,
          threshold: best_threshold,
          left: build_tree(data[left_indices], labels[left_indices], depth + 1),
          right: build_tree(data[right_indices], labels[right_indices], depth + 1)
        }
        
        node
      end
      
      def find_best_split(data, labels)
        best_gain = 0
        best_feature = nil
        best_threshold = nil
        
        (0...data[0].length).each do |feature|
          unique_values = data.map { |sample| sample[feature] }.uniq.sort
          
          unique_values.each do |threshold|
            left_indices, right_indices = split_data(data, feature, threshold)
            
            next if left_indices.empty? || right_indices.empty?
            
            gain = information_gain(labels, labels[left_indices], labels[right_indices])
            
            if gain > best_gain
              best_gain = gain
              best_feature = feature
              best_threshold = threshold
            end
          end
        end
        
        [best_feature, best_threshold]
      end
      
      def split_data(data, feature, threshold)
        left_indices = []
        right_indices = []
        
        data.each_with_index do |sample, index|
          if sample[feature] <= threshold
            left_indices << index
          else
            right_indices << index
          end
        end
        
        [left_indices, right_indices]
      end
      
      def information_gain(parent_labels, left_labels, right_labels)
        parent_entropy = calculate_entropy(parent_labels)
        left_entropy = calculate_entropy(left_labels)
        right_entropy = calculate_entropy(right_labels)
        
        left_weight = left_labels.length.to_f / parent_labels.length
        right_weight = right_labels.length.to_f / parent_labels.length
        
        parent_entropy - (left_weight * left_entropy + right_weight * right_entropy)
      end
      
      def calculate_entropy(labels)
        return 0 if labels.empty?
        
        label_counts = Hash.new(0)
        labels.each { |label| label_counts[label] += 1 }
        
        entropy = 0
        label_counts.each do |_, count|
          probability = count.to_f / labels.length
          entropy -= probability * Math.log2(probability) if probability > 0
        end
        
        entropy
      end
      
      def all_same_labels?(labels)
        labels.uniq.length == 1
      end
      
      def create_leaf(labels)
        majority_label = labels.group_by(&:itself).max_by { |_, group| group.length }.first
        { label: majority_label, leaf: true }
      end
      
      def traverse_tree(node, sample)
        return node[:label] if node[:leaf]
        
        if sample[node[:feature]] <= node[:threshold]
          traverse_tree(node[:left], sample)
        else
          traverse_tree(node[:right], sample)
        end
      end
    end
    
    # Decision tree demonstration
    puts "\nDecision Tree Classification:"
    
    # Sample dataset: weather conditions for playing tennis
    weather_data = [
      [[0, 0, 0], "No"],   # Sunny, Cool, High -> No
      [[0, 0, 1], "No"],   # Sunny, Cool, Normal -> No
      [[0, 1, 0], "Yes"],  # Sunny, Mild, High -> Yes
      [[0, 1, 1], "Yes"],  # Sunny, Mild, Normal -> Yes
      [[1, 0, 0], "Yes"],  # Cloudy, Cool, High -> Yes
      [[1, 0, 1], "Yes"],  # Cloudy, Cool, Normal -> Yes
      [[1, 1, 0], "Yes"],  # Cloudy, Mild, High -> Yes
      [[1, 1, 1], "Yes"],  # Cloudy, Mild, Normal -> Yes
      [[2, 0, 0], "No"],   # Rainy, Cool, High -> No
      [[2, 0, 1], "No"],   # Rainy, Cool, Normal -> No
      [[2, 1, 0], "Yes"],  # Rainy, Mild, High -> Yes
      [[2, 1, 1], "No"]    # Rainy, Mild, Normal -> No
    ]
    
    # Feature names: 0=Outlook (0=Sunny, 1=Cloudy, 2=Rainy), 1=Temperature (0=Cool, 1=Mild), 2=Humidity (0=High, 1=Normal)
    
    data = weather_data.map { |sample, _| sample }
    labels = weather_data.map { |_, label| label }
    
    # Train decision tree
    tree = DecisionTree.new(3)
    tree.fit(data, labels)
    
    # Test predictions
    test_samples = [
      [[0, 1, 0], "Sunny, Mild, High"],
      [[1, 0, 1], "Cloudy, Cool, Normal"],
      [[2, 1, 0], "Rainy, Mild, High"]
    ]
    
    puts "Decision Tree Predictions:"
    test_samples.each do |sample, description|
      prediction = tree.predict(sample)
      puts "  #{description} -> #{prediction}"
    end
    
    @examples << {
      title: "Decision Trees",
      description: "Decision tree implementation with information gain",
      code: <<~RUBY
        class DecisionTree
          def fit(data, labels)
            @root = build_tree(data, labels, 0)
          end
          
          def build_tree(data, labels, depth)
            return create_leaf(labels) if depth >= @max_depth
            best_feature, best_threshold = find_best_split(data, labels)
            # Build tree recursively
          end
        end
      RUBY
    }
    
    puts "\n✅ Decision Trees example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def kmeans_clustering
    puts "\n🎯 Example 3: K-Means Clustering"
    puts "=" * 50
    puts "Implementing K-means clustering algorithm."
    puts ""
    
    # K-means implementation
    puts "🎯 K-Means Clustering Implementation:"
    
    class KMeans
      def initialize(k, max_iterations = 100)
        @k = k
        @max_iterations = max_iterations
        @centroids = []
        @clusters = []
      end
      
      def fit(data)
        initialize_centroids(data)
        
        @max_iterations.times do |iteration|
          @clusters = assign_clusters(data)
          new_centroids = update_centroids(data)
          
          if converged?(new_centroids)
            puts "Converged after #{iteration} iterations"
            break
          end
          
          @centroids = new_centroids
        end
      end
      
      def predict(sample)
        distances = @centroids.map { |centroid| euclidean_distance(sample, centroid) }
        distances.index(distances.min)
      end
      
      def get_clusters
        @clusters
      end
      
      def get_centroids
        @centroids
      end
      
      private
      
      def initialize_centroids(data)
        # Randomly select k data points as initial centroids
        @centroids = data.sample(@k)
      end
      
      def assign_clusters(data)
        clusters = Array.new(@k) { [] }
        
        data.each_with_index do |sample, index|
          distances = @centroids.map { |centroid| euclidean_distance(sample, centroid) }
          cluster_index = distances.index(distances.min)
          clusters[cluster_index] << index
        end
        
        clusters
      end
      
      def update_centroids(data)
        new_centroids = []
        
        @clusters.each_with_index do |cluster_indices, cluster_id|
          next if cluster_indices.empty?
          
          cluster_data = cluster_indices.map { |index| data[index] }
          new_centroid = calculate_centroid(cluster_data)
          new_centroids << new_centroid
        end
        
        # Handle empty clusters by keeping old centroid
        while new_centroids.length < @k
          new_centroids << @centroids[new_centroids.length]
        end
        
        new_centroids
      end
      
      def calculate_centroid(cluster_data)
        num_features = cluster_data[0].length
        centroid = Array.new(num_features, 0.0)
        
        cluster_data.each do |sample|
          sample.each_with_index do |value, index|
            centroid[index] += value
          end
        end
        
        centroid.map! { |sum| sum / cluster_data.length }
        centroid
      end
      
      def euclidean_distance(point1, point2)
        sum_of_squares = point1.zip(point2).map { |a, b| (a - b) ** 2 }.sum
        Math.sqrt(sum_of_squares)
      end
      
      def converged?(new_centroids)
        return false if @centroids.empty?
        
        @centroids.zip(new_centroids).all? do |old, new|
          euclidean_distance(old, new) < 0.001
        end
      end
    end
    
    # K-means demonstration
    puts "\nK-Means Clustering Demo:"
    
    # Generate sample data
    def generate_sample_data
      # Create 3 clusters of data
      cluster1 = 10.times.map { [rand(1.0..3.0), rand(1.0..3.0)] }
      cluster2 = 10.times.map { [rand(5.0..7.0), rand(1.0..3.0)] }
      cluster3 = 10.times.map { [rand(3.0..5.0), rand(5.0..7.0)] }
      
      cluster1 + cluster2 + cluster3
    end
    
    data = generate_sample_data
    
    # Apply K-means
    kmeans = KMeans.new(3)
    kmeans.fit(data)
    
    # Display results
    puts "\nClustering Results:"
    puts "Number of clusters: #{kmeans.get_clusters.length}"
    
    kmeans.get_clusters.each_with_index do |cluster, index|
      puts "Cluster #{index}: #{cluster.length} points"
      puts "  Centroid: #{kmeans.get_centroids[index].map { |x| x.round(2) }}"
    end
    
    # Visual representation (text-based)
    puts "\nCluster Visualization:"
    grid = Array.new(10) { Array.new(10, '.') }
    
    data.each_with_index do |sample, index|
      cluster_id = kmeans.predict(sample)
      x = (sample[0] * 2).to_i
      y = (sample[1] * 2).to_i
      
      if x < 10 && y < 10
        grid[y][x] = cluster_id.to_s
      end
    end
    
    puts "Grid (0=cluster0, 1=cluster1, 2=cluster2):"
    grid.each { |row| puts row.join(' ') }
    
    # Test with new data
    puts "\nPredicting new samples:"
    test_samples = [
      [2.0, 2.0],  # Should be cluster 0
      [6.0, 2.0],  # Should be cluster 1
      [4.0, 6.0]   # Should be cluster 2
    ]
    
    test_samples.each do |sample|
      cluster = kmeans.predict(sample)
      puts "  Sample #{sample} -> Cluster #{cluster}"
    end
    
    @examples << {
      title: "K-Means Clustering",
      description: "K-means clustering algorithm implementation",
      code: <<~RUBY
        class KMeans
          def initialize(k, max_iterations = 100)
            @k = k
            @max_iterations = max_iterations
          end
          
          def fit(data)
            initialize_centroids(data)
            @max_iterations.times do
              @clusters = assign_clusters(data)
              @centroids = update_centroids(data)
            end
          end
        end
      RUBY
    }
    
    puts "\n✅ K-Means Clustering example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All AI/ML Examples"
    puts "=" * 50
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate AI/ML concepts!"
  end
end

if __FILE__ == $0
  examples = AIMachineLearningExamples.new
  examples.start_examples
end
