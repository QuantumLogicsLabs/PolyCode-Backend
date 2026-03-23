# Machine Learning Fundamentals in Ruby

## Overview

Machine Learning is a subset of artificial intelligence that enables systems to learn and improve from experience without being explicitly programmed. Ruby provides several libraries and frameworks for implementing machine learning algorithms.

## Key Concepts

### Supervised Learning
- **Classification**: Predicting discrete categories
- **Regression**: Predicting continuous values
- **Training Data**: Labeled examples for learning

### Unsupervised Learning
- **Clustering**: Grouping similar data points
- **Dimensionality Reduction**: Reducing feature space
- **Pattern Discovery**: Finding hidden patterns

### Reinforcement Learning
- **Agent**: Learning entity that makes decisions
- **Environment**: Context in which agent operates
- **Rewards**: Feedback for agent actions

## Ruby ML Libraries

### 1. Ruby-ML
```ruby
require 'ruby-ml'

# Linear regression example
regression = RubyML::LinearRegression.new
regression.fit(X_train, y_train)
predictions = regression.predict(X_test)
```

### 2. SciRuby
```ruby
require 'nmatrix'
require 'gsl'

# Matrix operations for ML
features = NMatrix.new([100, 10], dtype: :float64)
labels = NMatrix.new([100, 1], dtype: :float64)
```

### 3. TensorFlow.rb
```ruby
require 'tensorflow'

# Neural network with TensorFlow
model = TensorFlow::Graph.new
# Build and train model
```

## Data Preprocessing

### Feature Scaling
```ruby
def normalize_features(data)
  mean = data.sum / data.length
  std = Math.sqrt(data.map { |x| (x - mean) ** 2 }.sum / data.length)
  data.map { |x| (x - mean) / std }
end
```

### Data Splitting
```ruby
def train_test_split(data, test_ratio = 0.2)
  shuffled = data.shuffle
  split_index = (shuffled.length * (1 - test_ratio)).to_i
  [shuffled[0...split_index], shuffled[split_index..-1]]
end
```

## Model Evaluation

### Metrics
- **Accuracy**: Correct predictions / Total predictions
- **Precision**: True positives / (True positives + False positives)
- **Recall**: True positives / (True positives + False negatives)
- **F1 Score**: Harmonic mean of precision and recall

### Cross-Validation
```ruby
def cross_validate(model, data, folds = 5)
  fold_size = data.length / folds
  scores = []
  
  folds.times do |i|
    start_idx = i * fold_size
    end_idx = start_idx + fold_size
    
    test_data = data[start_idx...end_idx]
    train_data = data[0...start_idx] + data[end_idx..-1]
    
    model.fit(train_data)
    score = model.score(test_data)
    scores << score
  end
  
  scores.sum / scores.length
end
```

## Common Algorithms

### K-Nearest Neighbors
```ruby
class KNN
  def initialize(k = 3)
    @k = k
  end
  
  def fit(X_train, y_train)
    @X_train = X_train
    @y_train = y_train
  end
  
  def predict(X_test)
    X_test.map do |x|
      distances = @X_train.map { |train_x| euclidean_distance(x, train_x) }
      nearest_indices = distances.each_index.sort.first(@k)
      nearest_labels = nearest_indices.map { |i| @y_train[i] }
      nearest_labels.group_by(&:itself).max_by { |_, v| v.length }.first
    end
  end
  
  private
  
  def euclidean_distance(a, b)
    Math.sqrt(a.zip(b).map { |x, y| (x - y) ** 2 }.sum)
  end
end
```

### Simple Linear Regression
```ruby
class LinearRegression
  def fit(X, y)
    n = X.length
    sum_x = X.sum
    sum_y = y.sum
    sum_xy = X.zip(y).map { |x, y_i| x * y_i }.sum
    sum_x2 = X.map { |x| x ** 2 }.sum
    
    @slope = (n * sum_xy - sum_x * sum_y) / (n * sum_x2 - sum_x ** 2)
    @intercept = (sum_y - @slope * sum_x) / n
  end
  
  def predict(x)
    @slope * x + @intercept
  end
end
```

## Best Practices

1. **Data Quality**: Ensure clean, consistent data
2. **Feature Engineering**: Create meaningful features
3. **Model Selection**: Choose appropriate algorithms
4. **Hyperparameter Tuning**: Optimize model parameters
5. **Validation**: Use proper validation techniques

## Challenges in Ruby ML

- **Performance**: Ruby is slower than Python for numerical computations
- **Ecosystem**: Fewer ML libraries compared to Python
- **Memory Usage**: Higher memory consumption for large datasets
- **Community**: Smaller ML community in Ruby

## When to Use Ruby for ML

- **Web Integration**: When ML needs to be integrated with Rails applications
- **Prototyping**: Quick development and testing
- **Educational Purposes**: Learning ML concepts
- **Small to Medium Datasets**: When performance is not critical

## Conclusion

While Ruby may not be the first choice for large-scale machine learning, it offers a viable option for certain use cases, especially when integrating ML capabilities into existing Ruby applications.

## Further Reading

- [Ruby-ML Documentation](https://github.com/anic/ruby-ml)
- [SciRuby Project](http://sciruby.com/)
- [TensorFlow.rb](https://github.com/asicfr/tensorflow.rb)
