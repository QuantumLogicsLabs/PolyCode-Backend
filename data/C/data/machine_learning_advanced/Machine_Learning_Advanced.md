# Advanced Machine Learning

This file contains comprehensive advanced machine learning examples in C, including neural networks, decision trees, support vector machines, clustering algorithms, linear regression, and ensemble methods.

## 🤖 Machine Learning Fundamentals

### 🎯 ML Concepts
- **Supervised Learning**: Classification and regression algorithms
- **Unsupervised Learning**: Clustering and dimensionality reduction
- **Deep Learning**: Neural networks with multiple layers
- **Ensemble Methods**: Combining multiple models for better performance
- **Feature Engineering**: Data preprocessing and transformation

### 📊 Mathematical Foundations
- **Linear Algebra**: Matrix operations and vector spaces
- **Calculus**: Gradient descent and optimization
- **Probability Theory**: Statistical inference and distributions
- **Information Theory**: Entropy and information gain
- **Optimization Theory**: Convex optimization and gradient methods

## 🧠 Neural Networks

### Activation Functions
```c
// Activation functions
typedef enum {
    ACTIVATION_SIGMOID = 0,
    ACTIVATION_TANH = 1,
    ACTIVATION_RELU = 2,
    ACTIVATION_LEAKY_RELU = 3,
    ACTIVATION_SOFTMAX = 4,
    ACTIVATION_LINEAR = 5
} ActivationType;
```

### Neural Network Structure
```c
// Neural network structure
typedef struct {
    Layer* layers;
    int layer_count;
    LossType loss_type;
    OptimizerType optimizer_type;
    double learning_rate;
    double momentum;
    double beta1; // Adam
    double beta2; // Adam
    double epsilon; // Adam
    int epochs;
    int batch_size;
    int verbose;
} NeuralNetwork;
```

### Activation Function Implementation
```c
// Sigmoid activation
double sigmoid(double x) {
    return 1.0 / (1.0 + exp(-x));
}

// Sigmoid derivative
double sigmoid_derivative(double x) {
    double s = sigmoid(x);
    return s * (1.0 - s);
}

// ReLU activation
double relu(double x) {
    return x > 0 ? x : 0;
}

// ReLU derivative
double relu_derivative(double x) {
    return x > 0 ? 1 : 0;
}

// Softmax activation
void softmax(double* inputs, int size, double* outputs) {
    double max_input = inputs[0];
    for (int i = 1; i < size; i++) {
        if (inputs[i] > max_input) {
            max_input = inputs[i];
        }
    }
    
    double sum = 0.0;
    for (int i = 0; i < size; i++) {
        outputs[i] = exp(inputs[i] - max_input);
        sum += outputs[i];
    }
    
    for (int i = 0; i < size; i++) {
        outputs[i] /= sum;
    }
}
```

**Neural Network Benefits**:
- **Universal Approximation**: Can approximate any continuous function
- **Non-linear Modeling**: Captures complex non-linear relationships
- **Feature Learning**: Automatically learns relevant features
- **Scalability**: Works with large datasets and complex problems

### Forward and Backward Propagation
```c
// Forward pass through network
void forwardPass(NeuralNetwork* network, double* inputs, double* outputs) {
    // Input layer
    Layer* input_layer = &network->layers[0];
    for (int i = 0; i < input_layer->neuron_count; i++) {
        Neuron* neuron = &input_layer->neurons[i];
        double sum = neuron->bias;
        
        for (int j = 0; j < neuron->input_size; j++) {
            sum += neuron->weights[j] * inputs[j];
        }
        
        neuron->output = sum;
        input_layer->outputs[i] = sum;
    }
    
    // Hidden and output layers
    for (int l = 1; l < network->layer_count; l++) {
        Layer* prev_layer = &network->layers[l - 1];
        Layer* current_layer = &network->layers[l];
        
        for (int i = 0; i < current_layer->neuron_count; i++) {
            Neuron* neuron = &current_layer->neurons[i];
            double sum = neuron->bias;
            
            for (int j = 0; j < prev_layer->neuron_count; j++) {
                sum += neuron->weights[j] * prev_layer->outputs[j];
            }
            
            neuron->output = sum;
            current_layer->outputs[i] = sum;
        }
        
        // Apply activation function
        apply_activation(current_layer->outputs, current_layer->neuron_count, 
                        current_layer->activation, current_layer->outputs);
    }
    
    // Copy outputs
    Layer* output_layer = &network->layers[network->layer_count - 1];
    for (int i = 0; i < output_layer->neuron_count; i++) {
        outputs[i] = output_layer->outputs[i];
    }
}

// Backward pass through network
void backwardPass(NeuralNetwork* network, double* inputs, double* targets) {
    // Calculate output layer deltas
    Layer* output_layer = &network->layers[network->layer_count - 1];
    
    for (int i = 0; i < output_layer->neuron_count; i++) {
        double output = output_layer->outputs[i];
        double target = targets[i];
        double error = target - output;
        
        // Calculate delta based on loss function
        if (network->loss_type == LOSS_MSE) {
            output_layer->deltas[i] = error * sigmoid_derivative(output);
        } else if (network->loss_type == LOSS_BINARY_CROSS_ENTROPY) {
            output_layer->deltas[i] = error;
        }
    }
    
    // Calculate hidden layer deltas
    for (int l = network->layer_count - 2; l >= 0; l--) {
        Layer* current_layer = &network->layers[l];
        Layer* next_layer = &network->layers[l + 1];
        
        for (int i = 0; i < current_layer->neuron_count; i++) {
            double error = 0.0;
            
            for (int j = 0; j < next_layer->neuron_count; j++) {
                error += next_layer->neurons[j].weights[i] * next_layer->deltas[j];
            }
            
            current_layer->deltas[i] = error * relu_derivative(current_layer->outputs[i]);
        }
    }
    
    // Update weights
    for (int l = 0; l < network->layer_count; l++) {
        Layer* layer = &network->layers[l];
        
        for (int i = 0; i < layer->neuron_count; i++) {
            Neuron* neuron = &layer->neurons[i];
            
            // Update bias
            neuron->bias += network->learning_rate * layer->deltas[i];
            
            // Update weights
            for (int j = 0; j < neuron->input_size; j++) {
                double input = (l == 0) ? inputs[j] : network->layers[l - 1].outputs[j];
                neuron->weights[j] += network->learning_rate * layer->deltas[i] * input;
            }
        }
    }
}
```

## 🌳 Decision Trees

### Tree Node Structure
```c
// Decision tree node
typedef struct TreeNode {
    NodeType type;
    int feature_index;
    double threshold;
    double value; // For leaf nodes
    int class_label; // For classification
    struct TreeNode* left;
    struct TreeNode* right;
    int sample_count;
    double* class_counts; // For classification
} TreeNode;
```

### Decision Tree Implementation
```c
// Calculate Gini impurity
double giniImpurity(double* class_counts, int total_samples, int n_classes) {
    double gini = 0.0;
    
    for (int i = 0; i < n_classes; i++) {
        double p = class_counts[i] / total_samples;
        gini += p * (1.0 - p);
    }
    
    return gini;
}

// Calculate information gain
double informationGain(double* parent_counts, double* left_counts, double* right_counts, 
                     int parent_samples, int left_samples, int right_samples, int n_classes) {
    double parent_gini = giniImpurity(parent_counts, parent_samples, n_classes);
    double left_gini = giniImpurity(left_counts, left_samples, n_classes);
    double right_gini = giniImpurity(right_counts, right_samples, n_classes);
    
    double left_weight = (double)left_samples / parent_samples;
    double right_weight = (double)right_samples / parent_samples;
    
    return parent_gini - (left_weight * left_gini + right_weight * right_gini);
}

// Find best split
void findBestSplit(Dataset* dataset, int* indices, int n_samples, int* features, int n_features,
                  int* best_feature, double* best_threshold, double* best_gain) {
    *best_gain = 0.0;
    *best_feature = -1;
    *best_threshold = 0.0;
    
    // Calculate parent class counts
    double parent_counts[MAX_CLASSES] = {0};
    for (int i = 0; i < n_samples; i++) {
        int class_label = (int)dataset->y[indices[i]];
        parent_counts[class_label]++;
    }
    
    // Try each feature
    for (int f = 0; f < n_features; f++) {
        int feature_idx = features[f];
        
        // Get unique values for this feature
        double values[MAX_SAMPLES];
        for (int i = 0; i < n_samples; i++) {
            values[i] = dataset->X[indices[i]][feature_idx];
        }
        
        // Try each possible threshold
        for (int i = 0; i < n_samples; i++) {
            double threshold = values[i];
            
            // Split samples
            int left_indices[MAX_SAMPLES];
            int right_indices[MAX_SAMPLES];
            int left_count = 0, right_count = 0;
            
            for (int j = 0; j < n_samples; j++) {
                if (dataset->X[indices[j]][feature_idx] <= threshold) {
                    left_indices[left_count++] = indices[j];
                } else {
                    right_indices[right_count++] = indices[j];
                }
            }
            
            // Calculate class counts for left and right
            double left_counts[MAX_CLASSES] = {0};
            double right_counts[MAX_CLASSES] = {0};
            
            for (int j = 0; j < left_count; j++) {
                int class_label = (int)dataset->y[left_indices[j]];
                left_counts[class_label]++;
            }
            
            for (int j = 0; j < right_count; j++) {
                int class_label = (int)dataset->y[right_indices[j]];
                right_counts[class_label]++;
            }
            
            // Calculate information gain
            double gain = informationGain(parent_counts, left_counts, right_counts,
                                        n_samples, left_count, right_count, dataset->n_classes);
            
            if (gain > *best_gain) {
                *best_gain = gain;
                *best_feature = feature_idx;
                *best_threshold = threshold;
            }
        }
    }
}
```

**Decision Tree Benefits**:
- **Interpretability**: Easy to understand and visualize
- **Non-parametric**: No assumptions about data distribution
- **Feature Importance**: Natural feature selection
- **Mixed Data Types**: Handles both numerical and categorical data

## 🎯 Support Vector Machines

### Kernel Types
```c
// Kernel types
typedef enum {
    KERNEL_LINEAR = 0,
    KERNEL_POLYNOMIAL = 1,
    KERNEL_RBF = 2,
    KERNEL_SIGMOID = 3
} KernelType;
```

### SVM Structure
```c
// SVM structure
typedef struct {
    KernelType kernel_type;
    double C; // Regularization parameter
    double gamma; // For RBF kernel
    double degree; // For polynomial kernel
    double coef0; // For polynomial and sigmoid kernels
    double** support_vectors;
    double* alphas;
    double b; // Bias
    int n_support_vectors;
    int n_features;
    int n_classes;
} SVM;
```

### Kernel Functions
```c
// Linear kernel
double linearKernel(double* x1, double* x2, int n_features) {
    double result = 0.0;
    for (int i = 0; i < n_features; i++) {
        result += x1[i] * x2[i];
    }
    return result;
}

// RBF kernel
double rbfKernel(double* x1, double* x2, int n_features, double gamma) {
    double distance = 0.0;
    for (int i = 0; i < n_features; i++) {
        double diff = x1[i] - x2[i];
        distance += diff * diff;
    }
    return exp(-gamma * distance);
}

// Polynomial kernel
double polynomialKernel(double* x1, double* x2, int n_features, double degree, double coef0) {
    double linear_result = linearKernel(x1, x2, n_features);
    return pow(linear_result + coef0, degree);
}
```

**SVM Benefits**:
- **Maximum Margin**: Finds optimal separating hyperplane
- **Kernel Trick**: Handles non-linear problems efficiently
- **Robustness**: Less prone to overfitting
- **High-dimensional**: Works well in high-dimensional spaces

## 📊 K-Means Clustering

### K-Means Structure
```c
// K-means structure
typedef struct {
    Cluster* clusters;
    int k;
    int n_features;
    int n_samples;
    int max_iterations;
    double tolerance;
    int* assignments;
    double inertia;
} KMeans;
```

### K-Means Implementation
```c
// Assign samples to clusters
void assignClusters(KMeans* kmeans, Dataset* dataset) {
    for (int i = 0; i < dataset->n_samples; i++) {
        double min_distance = DBL_MAX;
        int best_cluster = 0;
        
        for (int k = 0; k < kmeans->k; k++) {
            double distance = 0.0;
            
            for (int f = 0; f < dataset->n_features; f++) {
                double diff = dataset->X[i][f] - kmeans->clusters[k].centroid[f];
                distance += diff * diff;
            }
            
            distance = sqrt(distance);
            
            if (distance < min_distance) {
                min_distance = distance;
                best_cluster = k;
            }
        }
        
        kmeans->assignments[i] = best_cluster;
        kmeans->clusters[best_cluster].members[kmeans->clusters[best_cluster].member_count++] = i;
    }
}

// Update centroids
void updateCentroids(KMeans* kmeans, Dataset* dataset) {
    for (int k = 0; k < kmeans->k; k++) {
        // Reset centroid to zero
        for (int f = 0; f < dataset->n_features; f++) {
            kmeans->clusters[k].centroid[f] = 0.0;
        }
        
        // Sum all member samples
        for (int i = 0; i < kmeans->clusters[k].member_count; i++) {
            int sample_idx = kmeans->clusters[k].members[i];
            
            for (int f = 0; f < dataset->n_features; f++) {
                kmeans->clusters[k].centroid[f] += dataset->X[sample_idx][f];
            }
        }
        
        // Average the centroid
        if (kmeans->clusters[k].member_count > 0) {
            for (int f = 0; f < dataset->n_features; f++) {
                kmeans->clusters[k].centroid[f] /= kmeans->clusters[k].member_count;
            }
        }
    }
}
```

**K-Means Benefits**:
- **Simplicity**: Easy to understand and implement
- **Scalability**: Efficient for large datasets
- **Speed**: Fast convergence
- **Versatility**: Works with various data types

## 📈 Principal Component Analysis (PCA)

### PCA Structure
```c
// PCA structure
typedef struct {
    double* components;
    double* explained_variance;
    double* explained_variance_ratio;
    double* singular_values;
    double* mean;
    int n_components;
    int n_features;
    int n_samples;
} PCA;
```

### PCA Implementation
```c
// Compute covariance matrix
void computeCovarianceMatrix(double** X, int n_samples, int n_features, double** covariance) {
    double* mean = malloc(n_features * sizeof(double));
    
    // Calculate mean
    for (int j = 0; j < n_features; j++) {
        mean[j] = 0.0;
        for (int i = 0; i < n_samples; i++) {
            mean[j] += X[i][j];
        }
        mean[j] /= n_samples;
    }
    
    // Calculate covariance
    for (int i = 0; i < n_features; i++) {
        for (int j = 0; j < n_features; j++) {
            covariance[i][j] = 0.0;
            for (int k = 0; k < n_samples; k++) {
                covariance[i][j] += (X[k][i] - mean[i]) * (X[k][j] - mean[j]);
            }
            covariance[i][j] /= (n_samples - 1);
        }
    }
    
    free(mean);
}
```

**PCA Benefits**:
- **Dimensionality Reduction**: Reduces feature space while preserving information
- **Noise Reduction**: Removes noise and redundant features
- **Visualization**: Enables data visualization in lower dimensions
- **Decorrelation**: Creates uncorrelated features

## 📊 Linear Regression

### Linear Regression Structure
```c
// Linear regression structure
typedef struct {
    double* coefficients;
    double intercept;
    int n_features;
    double* residuals;
    double r_squared;
    int fit_intercept;
} LinearRegression;
```

### Linear Regression Implementation
```c
// Train linear regression using ordinary least squares
void trainLinearRegression(LinearRegression* lr, Dataset* dataset) {
    int n = dataset->n_samples;
    int p = dataset->n_features;
    
    // Create design matrix X and target vector y
    double** X = malloc(n * sizeof(double*));
    double* y = malloc(n * sizeof(double));
    
    for (int i = 0; i < n; i++) {
        X[i] = malloc((p + 1) * sizeof(double)); // +1 for intercept
        X[i][0] = 1.0; // Intercept term
        
        for (int j = 0; j < p; j++) {
            X[i][j + 1] = dataset->X[i][j];
        }
        
        y[i] = dataset->y[i];
    }
    
    // Solve using normal equations: β = (X^T * X)^(-1) * X^T * y
    // This is a simplified implementation
    
    // Calculate X^T * X
    double** XtX = malloc((p + 1) * sizeof(double*));
    for (int i = 0; i < p + 1; i++) {
        XtX[i] = malloc((p + 1) * sizeof(double));
        for (int j = 0; j < p + 1; j++) {
            XtX[i][j] = 0.0;
        }
    }
    
    for (int i = 0; i < n; i++) {
        for (int j = 0; j < p + 1; j++) {
            for (int k = 0; k < p + 1; k++) {
                XtX[j][k] += X[i][j] * X[i][k];
            }
        }
    }
    
    // Calculate X^T * y
    double* Xty = malloc((p + 1) * sizeof(double));
    for (int i = 0; i < p + 1; i++) {
        Xty[i] = 0.0;
        for (int j = 0; j < n; j++) {
            Xty[i] += X[j][i] * y[j];
        }
    }
    
    // Simplified solution (in practice, use matrix inversion library)
    lr->intercept = Xty[0] / n;
    for (int i = 0; i < p; i++) {
        lr->coefficients[i] = Xty[i + 1] / n;
    }
    
    // Calculate R-squared
    double y_mean = 0.0;
    for (int i = 0; i < n; i++) {
        y_mean += y[i];
    }
    y_mean /= n;
    
    double ss_total = 0.0;
    double ss_residual = 0.0;
    
    for (int i = 0; i < n; i++) {
        double y_pred = lr->intercept;
        for (int j = 0; j < p; j++) {
            y_pred += lr->coefficients[j] * dataset->X[i][j];
        }
        
        ss_total += (y[i] - y_mean) * (y[i] - y_mean);
        ss_residual += (y[i] - y_pred) * (y[i] - y_pred);
        lr->residuals[i] = y[i] - y_pred;
    }
    
    lr->r_squared = 1.0 - (ss_residual / ss_total);
    
    // Cleanup
    for (int i = 0; i < n; i++) {
        free(X[i]);
    }
    for (int i = 0; i < p + 1; i++) {
        free(XtX[i]);
    }
    free(X);
    free(y);
    free(XtX);
    free(Xty);
}
```

**Linear Regression Benefits**:
- **Interpretability**: Easy to understand coefficients
- **Fast Training**: Efficient computation
- **Baseline Model**: Good starting point for regression problems
- **Feature Importance**: Coefficients indicate feature importance

## 🎯 Ensemble Methods

### Random Forest Structure
```c
// Random forest structure
typedef struct {
    DecisionTree** trees;
    int n_trees;
    int max_depth;
    int min_samples_split;
    int min_samples_leaf;
    int max_features;
    int n_classes;
    int n_features;
    double* feature_importances;
} RandomForest;
```

### Ensemble Learning Benefits
- **Improved Accuracy**: Combines multiple models for better performance
- **Reduced Overfitting**: Averages out individual model errors
- **Robustness**: Less sensitive to noise and outliers
- **Versatility**: Works with various base learners

## 🔧 Best Practices

### 1. Data Preprocessing
```c
// Good: Proper data normalization
void normalizeData(double** X, int n_samples, int n_features) {
    double* means = malloc(n_features * sizeof(double));
    double* stds = malloc(n_features * sizeof(double));
    
    // Calculate means
    for (int j = 0; j < n_features; j++) {
        means[j] = 0.0;
        for (int i = 0; i < n_samples; i++) {
            means[j] += X[i][j];
        }
        means[j] /= n_samples;
    }
    
    // Calculate standard deviations
    for (int j = 0; j < n_features; j++) {
        stds[j] = 0.0;
        for (int i = 0; i < n_samples; i++) {
            double diff = X[i][j] - means[j];
            stds[j] += diff * diff;
        }
        stds[j] = sqrt(stds[j] / n_samples);
    }
    
    // Normalize
    for (int i = 0; i < n_samples; i++) {
        for (int j = 0; j < n_features; j++) {
            if (stds[j] > 1e-8) {
                X[i][j] = (X[i][j] - means[j]) / stds[j];
            }
        }
    }
    
    free(means);
    free(stds);
}

// Bad: No normalization
void trainWithoutNormalization(Dataset* dataset) {
    // Features with different scales can cause convergence issues
    trainNeuralNetwork(network, dataset);
}
```

### 2. Cross-Validation
```c
// Good: K-fold cross-validation
double crossValidate(DecisionTree* tree, Dataset* dataset, int k_folds) {
    double total_accuracy = 0.0;
    int fold_size = dataset->n_samples / k_folds;
    
    for (int fold = 0; fold < k_folds; fold++) {
        // Split data
        int test_start = fold * fold_size;
        int test_end = (fold + 1) * fold_size;
        
        // Train on training data
        // Test on validation data
        // Calculate accuracy
        total_accuracy += fold_accuracy;
    }
    
    return total_accuracy / k_folds;
}

// Bad: No validation
void trainWithoutValidation(Dataset* dataset) {
    trainDecisionTree(tree, dataset);
    // No way to know if model generalizes well
}
```

### 3. Hyperparameter Tuning
```c
// Good: Grid search for hyperparameters
void gridSearch(NeuralNetwork* network, Dataset* dataset) {
    double learning_rates[] = {0.001, 0.01, 0.1};
    int hidden_layers[] = {1, 2, 3};
    int neurons[] = {16, 32, 64};
    
    double best_score = 0.0;
    double best_lr = 0.01;
    int best_layers = 2;
    int best_neurons = 32;
    
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 3; j++) {
            for (int k = 0; k < 3; k++) {
                network->learning_rate = learning_rates[i];
                // Create network with specific architecture
                // Train and evaluate
                double score = evaluateModel(network, dataset);
                
                if (score > best_score) {
                    best_score = score;
                    best_lr = learning_rates[i];
                    best_layers = hidden_layers[j];
                    best_neurons = neurons[k];
                }
            }
        }
    }
}

// Bad: Fixed hyperparameters
void trainWithFixedParams(NeuralNetwork* network, Dataset* dataset) {
    network->learning_rate = 0.01; // Fixed value
    trainNeuralNetwork(network, dataset);
    // May not be optimal for this dataset
}
```

### 4. Memory Management
```c
// Good: Proper memory cleanup
void trainAndCleanup(Dataset* dataset) {
    NeuralNetwork* network = createNeuralNetwork(layer_sizes, 4, LOSS_MSE, OPTIMIZER_SGD);
    if (!network) return;
    
    trainNeuralNetwork(network, dataset);
    
    // Use network
    predictNeuralNetwork(network, test_sample);
    
    // Cleanup
    freeNeuralNetwork(network);
    freeDataset(dataset);
}

// Bad: Memory leaks
void trainAndLeak(Dataset* dataset) {
    NeuralNetwork* network = createNeuralNetwork(layer_sizes, 4, LOSS_MSE, OPTIMIZER_SGD);
    trainNeuralNetwork(network, dataset);
    // Forgot to free network - memory leak!
}
```

### 5. Numerical Stability
```c
// Good: Numerically stable operations
double stableSoftmax(double* inputs, int size, double* outputs) {
    // Subtract max for numerical stability
    double max_input = inputs[0];
    for (int i = 1; i < size; i++) {
        if (inputs[i] > max_input) {
            max_input = inputs[i];
        }
    }
    
    double sum = 0.0;
    for (int i = 0; i < size; i++) {
        outputs[i] = exp(inputs[i] - max_input);
        sum += outputs[i];
    }
    
    for (int i = 0; i < size; i++) {
        outputs[i] /= sum;
    }
    
    return sum;
}

// Bad: Numerically unstable softmax
void unstableSoftmax(double* inputs, int size, double* outputs) {
    double sum = 0.0;
    for (int i = 0; i < size; i++) {
        outputs[i] = exp(inputs[i]); // Can overflow
        sum += outputs[i];
    }
    
    for (int i = 0; i < size; i++) {
        outputs[i] /= sum;
    }
}
```

## ⚠️ Common Pitfalls

### 1. Overfitting
```c
// Wrong: Model too complex for data
void overfitModel(Dataset* dataset) {
    DecisionTree* tree = createDecisionTree(1000, 1, 1, dataset->n_features, dataset->n_classes);
    // Very deep tree will memorize training data
    trainDecisionTree(tree, dataset);
}

// Right: Appropriate model complexity
void balancedModel(Dataset* dataset) {
    DecisionTree* tree = createDecisionTree(10, 5, 2, dataset->n_features, dataset->n_classes);
    // Reasonable depth prevents overfitting
    trainDecisionTree(tree, dataset);
}
```

### 2. Data Leakage
```c
// Wrong: Using test data for training
void dataLeakage(Dataset* dataset) {
    // Normalize using entire dataset including test data
    normalizeData(dataset->X, dataset->n_samples, dataset->n_features);
    
    DataSplit* split = trainTestSplit(dataset, 0.2);
    // Test data influenced training process
}

// Right: Proper data separation
void properDataHandling(Dataset* dataset) {
    DataSplit* split = trainTestSplit(dataset, 0.2);
    
    // Normalize only training data
    normalizeData(split->X_train, split->n_train, dataset->n_features);
    
    // Apply same normalization to test data
    applyNormalization(split->X_test, split->n_test, dataset->n_features);
}
```

### 3. Ignoring Class Imbalance
```c
// Wrong: No handling of class imbalance
void ignoreImbalance(Dataset* dataset) {
    // Train normally even if classes are imbalanced
    trainNeuralNetwork(network, dataset);
}

// Right: Handle class imbalance
void handleImbalance(Dataset* dataset) {
    // Calculate class weights
    double* class_weights = calculateClassWeights(dataset);
    
    // Use weighted loss function
    trainWithClassWeights(network, dataset, class_weights);
}
```

### 4. Poor Feature Selection
```c
// Wrong: Using all features without selection
void useAllFeatures(Dataset* dataset) {
    // Train with all features, including irrelevant ones
    trainNeuralNetwork(network, dataset);
}

// Right: Feature selection
void selectFeatures(Dataset* dataset) {
    // Select most important features
    int* selected_features = selectTopFeatures(dataset, 10);
    
    // Train with selected features only
    Dataset* reduced_dataset = createReducedDataset(dataset, selected_features, 10);
    trainNeuralNetwork(network, reduced_dataset);
}
```

## 🔧 Real-World Applications

### 1. Image Classification
```c
// CNN for image classification
void classifyImage(double* image_pixels, int width, int height) {
    // Preprocess image
    double* flattened = flattenImage(image_pixels, width, height);
    normalizeImage(flattened, width * height);
    
    // Feed through neural network
    double* predictions = malloc(NUM_CLASSES * sizeof(double));
    forwardPass(cnn, flattened, predictions);
    
    // Get predicted class
    int predicted_class = argmax(predictions, NUM_CLASSES);
    
    free(flattened);
    free(predictions);
}
```

### 2. Time Series Prediction
```c
// LSTM for time series
void predictTimeSeries(double* historical_data, int history_length, double* future_predictions) {
    // Create sequences from historical data
    double** sequences = createSequences(historical_data, history_length);
    
    // Feed through LSTM network
    for (int i = 0; i < prediction_horizon; i++) {
        double* input = sequences[i];
        double* output = malloc(1 * sizeof(double));
        forwardPass(lstm, input, output);
        future_predictions[i] = output[0];
        free(output);
    }
    
    free(sequences);
}
```

### 3. Anomaly Detection
```c
// Autoencoder for anomaly detection
int detectAnomaly(double* data_point, int n_features) {
    // Encode and decode data point
    double* encoded = malloc(LATENT_DIM * sizeof(double));
    double* reconstructed = malloc(n_features * sizeof(double));
    
    forwardPass(encoder, data_point, encoded);
    forwardPass(decoder, encoded, reconstructed);
    
    // Calculate reconstruction error
    double error = calculateReconstructionError(data_point, reconstructed, n_features);
    
    free(encoded);
    free(reconstructed);
    
    return error > ANOMALY_THRESHOLD;
}
```

### 4. Recommendation Systems
```c
// Matrix factorization for recommendations
void predictRatings(double** user_item_matrix, int n_users, int n_items, 
                  int user_id, int* recommendations) {
    // Factorize matrix
    double** user_factors = malloc(n_users * sizeof(double*));
    double** item_factors = malloc(n_items * sizeof(double*));
    
    matrixFactorization(user_item_matrix, user_factors, item_factors, n_users, n_items, K);
    
    // Predict ratings for user
    double* predictions = malloc(n_items * sizeof(double));
    for (int i = 0; i < n_items; i++) {
        predictions[i] = dotProduct(user_factors[user_id], item_factors[i], K);
    }
    
    // Get top recommendations
    getTopN(predictions, n_items, recommendations, TOP_N);
    
    free(user_factors);
    free(item_factors);
    free(predictions);
}
```

## 📚 Further Reading

### Books
- "Pattern Recognition and Machine Learning" by Christopher Bishop
- "Machine Learning: A Probabilistic Perspective" by Kevin Murphy
- "Deep Learning" by Ian Goodfellow, Yoshua Bengio, and Aaron Courville
- "The Elements of Statistical Learning" by Hastie, Tibshirani, and Friedman

### Topics
- Deep learning architectures (CNNs, RNNs, Transformers)
- Reinforcement learning
- Bayesian machine learning
- Natural language processing
- Computer vision
- Graph neural networks

Advanced machine learning in C provides the foundation for building high-performance, efficient, and scalable ML systems. Master these techniques to create sophisticated machine learning applications that can handle complex real-world problems!
