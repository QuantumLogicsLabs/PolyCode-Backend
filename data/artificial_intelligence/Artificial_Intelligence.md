# Artificial Intelligence Algorithms

This file contains comprehensive artificial intelligence algorithms examples in C, including neural networks, decision trees, K-means clustering, genetic algorithms, linear regression, perceptron, and principal component analysis.

## 📚 Artificial Intelligence Fundamentals

### 🧠 AI Concepts
- **Machine Learning**: Algorithms that learn from data
- **Neural Networks**: Brain-inspired computational models
- **Supervised Learning**: Learning from labeled examples
- **Unsupervised Learning**: Finding patterns in unlabeled data
- **Optimization**: Finding optimal solutions to problems

### 🎯 AI Applications
- **Classification**: Categorizing data into classes
- **Regression**: Predicting continuous values
- **Clustering**: Grouping similar data points
- **Pattern Recognition**: Identifying patterns in data
- **Optimization**: Finding best solutions

## 🧠 Neural Networks

### Activation Functions
```c
// Activation functions
typedef enum {
    ACTIVATION_SIGMOID = 0,
    ACTIVATION_TANH = 1,
    ACTIVATION_RELU = 2,
    ACTIVATION_SOFTMAX = 3
} ActivationFunction;

double sigmoid(double x) {
    return 1.0 / (1.0 + exp(-x));
}

double sigmoid_derivative(double x) {
    double s = sigmoid(x);
    return s * (1.0 - s);
}

double tanh_activation(double x) {
    return tanh(x);
}

double tanh_derivative(double x) {
    double t = tanh(x);
    return 1.0 - t * t;
}

double relu(double x) {
    return x > 0.0 ? x : 0.0;
}

double relu_derivative(double x) {
    return x > 0.0 ? 1.0 : 0.0;
}
```

### Neural Network Structure
```c
// Neuron structure
typedef struct {
    double* weights;
    double bias;
    double output;
    double delta;
    int input_count;
    ActivationFunction activation;
} Neuron;

// Layer structure
typedef struct {
    Neuron* neurons;
    int neuron_count;
    double* outputs;
    double* deltas;
} Layer;

// Neural Network structure
typedef struct {
    Layer* layers;
    int layer_count;
    double learning_rate;
    int epoch_count;
    double training_loss;
    double validation_loss;
} NeuralNetwork;
```

### Neural Network Implementation
```c
// Create neural network
NeuralNetwork* createNeuralNetwork(int* layer_sizes, int layer_count, ActivationFunction activation) {
    NeuralNetwork* network = malloc(sizeof(NeuralNetwork));
    if (!network) return NULL;
    
    network->layers = malloc(layer_count * sizeof(Layer));
    if (!network->layers) {
        free(network);
        return NULL;
    }
    
    network->layer_count = layer_count;
    network->learning_rate = LEARNING_RATE;
    network->epoch_count = 0;
    network->training_loss = 0.0;
    network->validation_loss = 0.0;
    
    // Create layers
    for (int i = 0; i < layer_count; i++) {
        int input_count = (i == 0) ? layer_sizes[i] : layer_sizes[i-1];
        network->layers[i] = *createLayer(layer_sizes[i], input_count, activation);
    }
    
    return network;
}

// Forward propagation
void forwardPropagation(NeuralNetwork* network, double* inputs) {
    // Input layer
    for (int i = 0; i < network->layers[0].neuron_count; i++) {
        network->layers[0].outputs[i] = inputs[i];
    }
    
    // Hidden and output layers
    for (int l = 1; l < network->layer_count; l++) {
        Layer* layer = &network->layers[l];
        Layer* prev_layer = &network->layers[l-1];
        
        for (int n = 0; n < layer->neuron_count; n++) {
            Neuron* neuron = &layer->neurons[n];
            double sum = neuron->bias;
            
            // Calculate weighted sum
            for (int i = 0; i < neuron->input_count; i++) {
                sum += neuron->weights[i] * prev_layer->outputs[i];
            }
            
            // Apply activation function
            switch (neuron->activation) {
                case ACTIVATION_SIGMOID:
                    neuron->output = sigmoid(sum);
                    break;
                case ACTIVATION_TANH:
                    neuron->output = tanh_activation(sum);
                    break;
                case ACTIVATION_RELU:
                    neuron->output = relu(sum);
                    break;
                case ACTIVATION_SOFTMAX:
                    // Softmax is handled separately for the whole layer
                    neuron->output = sum; // Store raw sum first
                    break;
            }
            
            layer->outputs[n] = neuron->output;
        }
        
        // Apply softmax to the whole layer if needed
        if (layer->neurons[0].activation == ACTIVATION_SOFTMAX) {
            double total = 0.0;
            for (int n = 0; n < layer->neuron_count; n++) {
                layer->outputs[n] = exp(layer->outputs[n]);
                total += layer->outputs[n];
            }
            for (int n = 0; n < layer->neuron_count; n++) {
                layer->outputs[n] /= total;
            }
        }
    }
}

// Backward propagation
void backwardPropagation(NeuralNetwork* network, double* targets) {
    // Calculate output layer errors
    Layer* output_layer = &network->layers[network->layer_count - 1];
    
    for (int n = 0; n < output_layer->neuron_count; n++) {
        double output = output_layer->outputs[n];
        double target = targets[n];
        double error = target - output;
        
        // Calculate delta based on activation function
        switch (output_layer->neurons[n].activation) {
            case ACTIVATION_SIGMOID:
                output_layer->deltas[n] = error * sigmoid_derivative(output);
                break;
            case ACTIVATION_TANH:
                output_layer->deltas[n] = error * tanh_derivative(output);
                break;
            case ACTIVATION_RELU:
                output_layer->deltas[n] = error * relu_derivative(output);
                break;
            case ACTIVATION_SOFTMAX:
                output_layer->deltas[n] = error; // For softmax with cross-entropy
                break;
        }
    }
    
    // Calculate hidden layer errors
    for (int l = network->layer_count - 2; l >= 0; l--) {
        Layer* layer = &network->layers[l];
        Layer* next_layer = &network->layers[l + 1];
        
        for (int n = 0; n < layer->neuron_count; n++) {
            double error = 0.0;
            
            // Sum errors from next layer
            for (int m = 0; m < next_layer->neuron_count; m++) {
                error += next_layer->deltas[m] * next_layer->neurons[m].weights[n];
            }
            
            double output = layer->outputs[n];
            
            // Calculate delta based on activation function
            switch (layer->neurons[n].activation) {
                case ACTIVATION_SIGMOID:
                    layer->deltas[n] = error * sigmoid_derivative(output);
                    break;
                case ACTIVATION_TANH:
                    layer->deltas[n] = error * tanh_derivative(output);
                    break;
                case ACTIVATION_RELU:
                    layer->deltas[n] = error * relu_derivative(output);
                    break;
                default:
                    layer->deltas[n] = error;
                    break;
            }
        }
    }
    
    // Update weights and biases
    for (int l = 1; l < network->layer_count; l++) {
        Layer* layer = &network->layers[l];
        Layer* prev_layer = &network->layers[l - 1];
        
        for (int n = 0; n < layer->neuron_count; n++) {
            Neuron* neuron = &layer->neurons[n];
            
            // Update bias
            neuron->bias += network->learning_rate * layer->deltas[n];
            
            // Update weights
            for (int i = 0; i < neuron->input_count; i++) {
                neuron->weights[i] += network->learning_rate * layer->deltas[n] * prev_layer->outputs[i];
            }
        }
    }
}
```

**Neural Network Benefits**:
- **Universal Approximation**: Can approximate any continuous function
- **Non-linear Modeling**: Captures complex non-linear relationships
- **Feature Learning**: Automatically learns relevant features
- **Flexible Architecture**: Configurable layers and neurons

## 🌳 Decision Trees

### Decision Tree Structure
```c
// Decision tree node
typedef struct DecisionTreeNode {
    int feature_index;
    double threshold;
    int class_label;
    int is_leaf;
    struct DecisionTreeNode* left;
    struct DecisionTreeNode* right;
} DecisionTreeNode;

// Decision tree structure
typedef struct {
    DecisionTreeNode* root;
    int max_depth;
    int min_samples_split;
} DecisionTree;
```

### Gini Impurity
```c
// Calculate Gini impurity
double calculateGini(int* class_counts, int total_samples, int num_classes) {
    double gini = 1.0;
    
    for (int i = 0; i < num_classes; i++) {
        if (total_samples > 0) {
            double p = (double)class_counts[i] / total_samples;
            gini -= p * p;
        }
    }
    
    return gini;
}
```

### Decision Tree Building
```c
// Find best split
void findBestSplit(double** features, int* labels, int num_samples, int num_features,
                 int* best_feature, double* best_threshold, double* best_gini) {
    *best_gini = 1.0;
    *best_feature = -1;
    *best_threshold = 0.0;
    
    for (int feature = 0; feature < num_features; feature++) {
        // Sort samples by feature value
        for (int i = 0; i < num_samples - 1; i++) {
            for (int j = i + 1; j < num_samples; j++) {
                if (features[j][feature] < features[i][feature]) {
                    // Swap samples
                    double* temp_feature = features[i];
                    double temp_label = labels[i];
                    features[i] = features[j];
                    labels[i] = labels[j];
                    features[j] = temp_feature;
                    labels[j] = temp_label;
                }
            }
        }
        
        // Find best threshold for this feature
        for (int i = 0; i < num_samples - 1; i++) {
            double threshold = (features[i][feature] + features[i+1][feature]) / 2.0;
            
            int left_class_counts[10] = {0}; // Assuming max 10 classes
            int right_class_counts[10] = {0};
            int left_count = 0, right_count = 0;
            
            for (int j = 0; j < num_samples; j++) {
                if (features[j][feature] <= threshold) {
                    left_class_counts[labels[j]]++;
                    left_count++;
                } else {
                    right_class_counts[labels[j]]++;
                    right_count++;
                }
            }
            
            double left_gini = calculateGini(left_class_counts, left_count, 10);
            double right_gini = calculateGini(right_class_counts, right_count, 10);
            double weighted_gini = (left_gini * left_count + right_gini * right_count) / num_samples;
            
            if (weighted_gini < *best_gini) {
                *best_gini = weighted_gini;
                *best_feature = feature;
                *best_threshold = threshold;
            }
        }
    }
}

// Build decision tree
DecisionTreeNode* buildDecisionTree(double** features, int* labels, int num_samples, 
                                  int num_features, int depth) {
    DecisionTreeNode* node = malloc(sizeof(DecisionTreeNode));
    if (!node) return NULL;
    
    // Check if all samples have the same class
    int first_class = labels[0];
    int all_same = 1;
    for (int i = 1; i < num_samples; i++) {
        if (labels[i] != first_class) {
            all_same = 0;
            break;
        }
    }
    
    if (all_same || depth >= 5) { // Max depth = 5
        node->is_leaf = 1;
        node->class_label = first_class;
        node->left = NULL;
        node->right = NULL;
        return node;
    }
    
    // Find best split
    int best_feature;
    double best_threshold, best_gini;
    findBestSplit(features, labels, num_samples, num_features, 
                  &best_feature, &best_threshold, &best_gini);
    
    // Create split
    node->feature_index = best_feature;
    node->threshold = best_threshold;
    node->is_leaf = 0;
    
    // Split data
    double** left_features = malloc(num_samples * sizeof(double*));
    double** right_features = malloc(num_samples * sizeof(double*));
    int* left_labels = malloc(num_samples * sizeof(int));
    int* right_labels = malloc(num_samples * sizeof(int));
    int left_count = 0, right_count = 0;
    
    for (int i = 0; i < num_samples; i++) {
        if (features[i][best_feature] <= best_threshold) {
            left_features[left_count] = features[i];
            left_labels[left_count] = labels[i];
            left_count++;
        } else {
            right_features[right_count] = features[i];
            right_labels[right_count] = labels[i];
            right_count++;
        }
    }
    
    // Recursively build subtrees
    node->left = buildDecisionTree(left_features, left_labels, left_count, 
                                 num_features, depth + 1);
    node->right = buildDecisionTree(right_features, right_labels, right_count, 
                                  num_features, depth + 1);
    
    free(left_features);
    free(right_features);
    free(left_labels);
    free(right_labels);
    
    return node;
}
```

**Decision Tree Benefits**:
- **Interpretable**: Easy to understand and visualize
- **Non-parametric**: No assumptions about data distribution
- **Feature Selection**: Automatically selects important features
- **Fast Prediction**: Quick classification once trained

## 🎯 K-Means Clustering

### Cluster Structure
```c
// Cluster structure
typedef struct {
    double* centroid;
    int* samples;
    int sample_count;
    int cluster_id;
} Cluster;
```

### K-Means Algorithm
```c
// K-means clustering
Cluster* kMeansClustering(double** data, int num_samples, int num_features, 
                          int k, int max_iterations) {
    Cluster* clusters = malloc(k * sizeof(Cluster));
    if (!clusters) return NULL;
    
    // Initialize clusters with random centroids
    for (int i = 0; i < k; i++) {
        clusters[i].centroid = malloc(num_features * sizeof(double));
        clusters[i].samples = malloc(num_samples * sizeof(int));
        clusters[i].cluster_id = i;
        clusters[i].sample_count = 0;
        
        // Random centroid from data
        int random_sample = rand() % num_samples;
        for (int f = 0; f < num_features; f++) {
            clusters[i].centroid[f] = data[random_sample][f];
        }
    }
    
    // K-means iterations
    for (int iteration = 0; iteration < max_iterations; iteration++) {
        // Assign samples to nearest cluster
        for (int i = 0; i < k; i++) {
            clusters[i].sample_count = 0;
        }
        
        for (int sample = 0; sample < num_samples; sample++) {
            double min_distance = INFINITY;
            int closest_cluster = 0;
            
            for (int i = 0; i < k; i++) {
                double distance = 0.0;
                for (int f = 0; f < num_features; f++) {
                    double diff = data[sample][f] - clusters[i].centroid[f];
                    distance += diff * diff;
                }
                distance = sqrt(distance);
                
                if (distance < min_distance) {
                    min_distance = distance;
                    closest_cluster = i;
                }
            }
            
            clusters[closest_cluster].samples[clusters[closest_cluster].sample_count++] = sample;
        }
        
        // Update centroids
        for (int i = 0; i < k; i++) {
            if (clusters[i].sample_count > 0) {
                for (int f = 0; f < num_features; f++) {
                    clusters[i].centroid[f] = 0.0;
                    for (int s = 0; s < clusters[i].sample_count; s++) {
                        clusters[i].centroid[f] += data[clusters[i].samples[s]][f];
                    }
                    clusters[i].centroid[f] /= clusters[i].sample_count;
                }
            }
        }
    }
    
    return clusters;
}
```

**K-Means Benefits**:
- **Simple Algorithm**: Easy to understand and implement
- **Scalable**: Works well with large datasets
- **Fast Convergence**: Typically converges quickly
- **Unsupervised**: No labeled data required

## 🧬 Genetic Algorithm

### Individual Structure
```c
// Individual structure for genetic algorithm
typedef struct {
    double* genes;
    double fitness;
    int gene_count;
} Individual;

// Population structure
typedef struct {
    Individual* individuals;
    int population_size;
    int gene_count;
    double mutation_rate;
    double crossover_rate;
} Population;
```

### Genetic Algorithm Operations
```c
// Fitness function example (sphere function)
double fitnessFunction(double* genes, int gene_count) {
    double sum = 0.0;
    for (int i = 0; i < gene_count; i++) {
        sum += genes[i] * genes[i];
    }
    return 1.0 / (1.0 + sum); // Minimize sum of squares
}

// Selection (tournament selection)
Individual* selection(Population* population, int tournament_size) {
    Individual* best = NULL;
    
    for (int i = 0; i < tournament_size; i++) {
        int random_index = rand() % population->population_size;
        Individual* candidate = &population->individuals[random_index];
        
        if (!best || candidate->fitness > best->fitness) {
            best = candidate;
        }
    }
    
    return best;
}

// Crossover
void crossover(Individual* parent1, Individual* parent2, Individual* child1, Individual* child2) {
    int crossover_point = rand() % parent1->gene_count;
    
    for (int i = 0; i < crossover_point; i++) {
        child1->genes[i] = parent1->genes[i];
        child2->genes[i] = parent2->genes[i];
    }
    
    for (int i = crossover_point; i < parent1->gene_count; i++) {
        child1->genes[i] = parent2->genes[i];
        child2->genes[i] = parent1->genes[i];
    }
}

// Mutation
void mutate(Individual* individual, double mutation_rate) {
    for (int i = 0; i < individual->gene_count; i++) {
        if (((double)rand() / RAND_MAX) < mutation_rate) {
            individual->genes[i] = ((double)rand() / RAND_MAX) * 10.0 - 5.0;
        }
    }
}
```

### Genetic Algorithm Evolution
```c
// Genetic algorithm evolution
void evolvePopulation(Population* population, int generations) {
    for (int generation = 0; generation < generations; generation++) {
        Population* new_population = createPopulation(population->population_size, 
                                                      population->gene_count,
                                                      population->mutation_rate,
                                                      population->crossover_rate);
        
        // Elitism: keep best individual
        Individual* best = selection(population, population->population_size);
        new_population->individuals[0] = *best;
        
        // Generate new population
        for (int i = 1; i < population->population_size; i++) {
            Individual* parent1 = selection(population, 3);
            Individual* parent2 = selection(population, 3);
            
            if (((double)rand() / RAND_MAX) < population->crossover_rate) {
                crossover(parent1, parent2, 
                        &new_population->individuals[i], 
                        &new_population->individuals[i+1]);
                i++;
            } else {
                new_population->individuals[i] = *parent1;
                new_population->individuals[i+1] = *parent2;
                i++;
            }
        }
        
        // Mutation
        for (int i = 1; i < population->population_size; i++) {
            mutate(&new_population->individuals[i], population->mutation_rate);
            new_population->individuals[i].fitness = fitnessFunction(new_population->individuals[i].genes, 
                                                                population->gene_count);
        }
        
        // Replace old population
        free(population->individuals);
        population->individuals = new_population->individuals;
        
        if (generation % 10 == 0) {
            Individual* current_best = selection(population, population->population_size);
            printf("Generation %d, Best Fitness: %.6f\n", generation, current_best->fitness);
        }
    }
}
```

**Genetic Algorithm Benefits**:
- **Global Optimization**: Can find global optima
- **Parallelizable**: Can evaluate multiple solutions simultaneously
- **Robust**: Works well with noisy or complex fitness landscapes
- **Flexible**: Adaptable to various optimization problems

## 📈 Linear Regression

### Linear Regression Implementation
```c
void demonstrateLinearRegression() {
    // Generate sample data
    double x[100];
    double y[100];
    
    for (int i = 0; i < 100; i++) {
        x[i] = (double)i / 10.0;
        y[i] = 2.0 * x[i] + 1.0 + ((double)rand() / RAND_MAX - 0.5) * 0.5; // y = 2x + 1 + noise
    }
    
    // Calculate linear regression coefficients
    double sum_x = 0, sum_y = 0, sum_xy = 0, sum_x2 = 0;
    int n = 100;
    
    for (int i = 0; i < n; i++) {
        sum_x += x[i];
        sum_y += y[i];
        sum_xy += x[i] * y[i];
        sum_x2 += x[i] * x[i];
    }
    
    double slope = (n * sum_xy - sum_x * sum_y) / (n * sum_x2 - sum_x * sum_x);
    double intercept = (sum_y - slope * sum_x) / n;
    
    printf("Linear regression results:\n");
    printf("Slope: %.4f\n", slope);
    printf("Intercept: %.4f\n", intercept);
    printf("Equation: y = %.4fx + %.4f\n", slope, intercept);
    
    // Calculate R-squared
    double mean_y = sum_y / n;
    double ss_tot = 0, ss_res = 0;
    
    for (int i = 0; i < n; i++) {
        ss_tot += (y[i] - mean_y) * (y[i] - mean_y);
        double predicted = slope * x[i] + intercept;
        ss_res += (y[i] - predicted) * (y[i] - predicted);
    }
    
    double r_squared = 1.0 - (ss_res / ss_tot);
    printf("R-squared: %.4f\n", r_squared);
}
```

**Linear Regression Benefits**:
- **Simple Interpretation**: Easy to understand coefficients
- **Fast Computation**: Efficient to calculate
- **Baseline Model**: Good starting point for regression
- **Statistical Foundation**: Well-understood statistical properties

## 🎯 Perceptron

### Perceptron Implementation
```c
void demonstratePerceptron() {
    // Create sample data for linear classification
    double features[100][2];
    int labels[100];
    
    // Generate two linearly separable classes
    for (int i = 0; i < 50; i++) {
        features[i][0] = ((double)rand() / RAND_MAX) * 4.0 - 2.0;
        features[i][1] = ((double)rand() / RAND_MAX) * 4.0 - 2.0;
        labels[i] = (features[i][0] + features[i][1] > 0) ? 1 : 0; // Class 0: x + y > 0
    }
    
    for (int i = 50; i < 100; i++) {
        features[i][0] = ((double)rand() / RAND_MAX) * 4.0 - 2.0;
        features[i][1] = ((double)rand() / RAND_MAX) * 4.0 - 2.0;
        labels[i] = (features[i][0] + features[i][1] < 0) ? 1 : 0; // Class 1: x + y < 0
    }
    
    // Initialize perceptron weights
    double weights[3] = {0.0, 0.0, 0.0}; // w0, w1, w2 (including bias)
    double learning_rate = 0.1;
    int epochs = 100;
    
    // Train perceptron
    for (int epoch = 0; epoch < epochs; epoch++) {
        int errors = 0;
        
        for (int i = 0; i < 100; i++) {
            // Calculate prediction
            double activation = weights[0] + weights[1] * features[i][0] + weights[2] * features[i][1];
            int prediction = (activation > 0) ? 1 : 0;
            
            // Update weights if prediction is wrong
            if (prediction != labels[i]) {
                errors++;
                for (int j = 0; j < 3; j++) {
                    double input = (j == 0) ? 1.0 : features[i][j-1];
                    weights[j] += learning_rate * (labels[i] - prediction) * input;
                }
            }
        }
        
        if (epoch % 10 == 0) {
            printf("Epoch %d, Errors: %d\n", epoch, errors);
        }
        
        if (errors == 0) break; // Converged
    }
    
    printf("Perceptron training completed\n");
    printf("Final weights: w0=%.4f, w1=%.4f, w2=%.4f\n", weights[0], weights[1], weights[2]);
    printf("Decision boundary: %.4fx + %.4fy + %.4f = 0\n", weights[1], weights[2], weights[0]);
}
```

**Perceptron Benefits**:
- **Simple Algorithm**: Easy to understand and implement
- **Linear Classification**: Good for linearly separable problems
- **Online Learning**: Can learn from streaming data
- **Foundation**: Basis for more complex neural networks

## 📊 Principal Component Analysis

### PCA Implementation
```c
void demonstratePrincipalComponentAnalysis() {
    // Create sample data (2D points)
    double data[100][2];
    
    // Generate correlated data
    for (int i = 0; i < 100; i++) {
        double angle = ((double)rand() / RAND_MAX) * 2 * PI;
        double radius = ((double)rand() / RAND_MAX) * 2.0 + 1.0;
        data[i][0] = radius * cos(angle);
        data[i][1] = radius * sin(angle) * 0.5; // Elliptical distribution
    }
    
    // Calculate mean
    double mean_x = 0, mean_y = 0;
    for (int i = 0; i < 100; i++) {
        mean_x += data[i][0];
        mean_y += data[i][1];
    }
    mean_x /= 100;
    mean_y /= 100;
    
    // Center the data
    for (int i = 0; i < 100; i++) {
        data[i][0] -= mean_x;
        data[i][1] -= mean_y;
    }
    
    // Calculate covariance matrix
    double cov[2][2] = {{0}};
    for (int i = 0; i < 100; i++) {
        cov[0][0] += data[i][0] * data[i][0];
        cov[0][1] += data[i][0] * data[i][1];
        cov[1][0] += data[i][1] * data[i][0];
        cov[1][1] += data[i][1] * data[i][1];
    }
    cov[0][0] /= 99; // n-1 for sample covariance
    cov[0][1] /= 99;
    cov[1][0] /= 99;
    cov[1][1] /= 99;
    
    printf("Covariance matrix:\n");
    printf("[%.4f, %.4f]\n", cov[0][0], cov[0][1]);
    printf("[%.4f, %.4f]\n", cov[1][0], cov[1][1]);
    
    // Calculate eigenvalues (simplified for 2x2 matrix)
    double trace = cov[0][0] + cov[1][1];
    double det = cov[0][0] * cov[1][1] - cov[0][1] * cov[1][0];
    double discriminant = trace * trace - 4 * det;
    
    double eigenvalue1 = (trace + sqrt(discriminant)) / 2;
    double eigenvalue2 = (trace - sqrt(discriminant)) / 2;
    
    printf("\nEigenvalues:\n");
    printf("λ1 = %.4f (%.2f%% variance)\n", eigenvalue1, 
           eigenvalue1 / (eigenvalue1 + eigenvalue2) * 100);
    printf("λ2 = %.4f (%.2f%% variance)\n", eigenvalue2, 
           eigenvalue2 / (eigenvalue1 + eigenvalue2) * 100);
    
    // Calculate eigenvectors (simplified)
    double eigenvector1[2], eigenvector2[2];
    
    // For eigenvalue1
    if (fabs(cov[0][1]) > 1e-6) {
        eigenvector1[0] = 1.0;
        eigenvector1[1] = (eigenvalue1 - cov[0][0]) / cov[0][1];
    } else {
        eigenvector1[0] = 0.0;
        eigenvector1[1] = 1.0;
    }
    
    // Normalize eigenvector
    double norm1 = sqrt(eigenvector1[0] * eigenvector1[0] + eigenvector1[1] * eigenvector1[1]);
    eigenvector1[0] /= norm1;
    eigenvector1[1] /= norm1;
    
    printf("\nPrincipal component (first eigenvector):\n");
    printf("PC1: (%.4f, %.4f)\n", eigenvector1[0], eigenvector1[1]);
    
    // Project data onto principal component
    printf("\nProjecting data onto principal component...\n");
    double projected[100];
    double variance = 0.0;
    
    for (int i = 0; i < 100; i++) {
        projected[i] = data[i][0] * eigenvector1[0] + data[i][1] * eigenvector1[1];
        variance += projected[i] * projected[i];
    }
    
    variance /= 99; // n-1 for sample variance
    printf("Variance along PC1: %.4f\n", variance);
}
```

**PCA Benefits**:
- **Dimensionality Reduction**: Reduces feature space while preserving information
- **Noise Reduction**: Removes noise and unimportant features
- **Visualization**: Enables visualization of high-dimensional data
- **Feature Extraction**: Creates new features that capture maximum variance

## 🔧 Best Practices

### 1. Neural Network Training
```c
// Good: Proper weight initialization
for (int i = 0; i < input_count; i++) {
    // Xavier initialization
    double range = sqrt(6.0 / (input_count + output_count));
    neuron->weights[i] = ((double)rand() / RAND_MAX) * 2.0 * range - range;
}

// Bad: Poor weight initialization
for (int i = 0; i < input_count; i++) {
    neuron->weights[i] = 0.0; // All weights zero prevents learning
}
```

### 2. Data Preprocessing
```c
// Good: Normalize input data
void normalizeData(double** data, int num_samples, int num_features) {
    for (int f = 0; f < num_features; f++) {
        double mean = 0.0, std = 0.0;
        
        // Calculate mean
        for (int i = 0; i < num_samples; i++) {
            mean += data[i][f];
        }
        mean /= num_samples;
        
        // Calculate standard deviation
        for (int i = 0; i < num_samples; i++) {
            double diff = data[i][f] - mean;
            std += diff * diff;
        }
        std = sqrt(std / num_samples);
        
        // Normalize
        for (int i = 0; i < num_samples; i++) {
            data[i][f] = (data[i][f] - mean) / std;
        }
    }
}

// Bad: No normalization
// Using raw data can cause convergence issues
```

### 3. Overfitting Prevention
```c
// Good: Early stopping and validation
void trainWithValidation(NeuralNetwork* network, double** train_inputs, double** train_targets,
                        double** val_inputs, double** val_targets, int train_count, int val_count) {
    double best_val_loss = INFINITY;
    int patience = 10;
    int patience_counter = 0;
    
    for (int epoch = 0; epoch < EPOCHS; epoch++) {
        // Train on training set
        trainNeuralNetwork(network, train_inputs, train_targets, train_count, 1);
        
        // Validate on validation set
        double val_loss = calculateLoss(network, val_inputs, val_targets, val_count);
        
        if (val_loss < best_val_loss) {
            best_val_loss = val_loss;
            patience_counter = 0;
            // Save best model weights
        } else {
            patience_counter++;
            if (patience_counter >= patience) {
                printf("Early stopping at epoch %d\n", epoch);
                break;
            }
        }
    }
}

// Bad: No validation or early stopping
trainNeuralNetwork(network, inputs, targets, count, EPOCHS); // May overfit
```

### 4. Learning Rate Management
```c
// Good: Learning rate scheduling
double calculateLearningRate(int epoch, double initial_lr) {
    // Exponential decay
    return initial_lr * exp(-0.001 * epoch);
}

// Bad: Fixed learning rate
double learning_rate = 0.01; // Never changes, may converge slowly or overshoot
```

### 5. Random Seed Management
```c
// Good: Set random seed for reproducibility
void setRandomSeed(unsigned int seed) {
    srand(seed);
    printf("Random seed set to %u\n", seed);
}

// Bad: No seed control
// Results are not reproducible
```

## ⚠️ Common Pitfalls

### 1. Vanishing Gradients
```c
// Wrong: Deep network with sigmoid activation
int layers[] = {784, 512, 256, 128, 64, 32, 16, 10}; // Too deep with sigmoid

// Right: Use ReLU activation and proper initialization
int layers[] = {784, 256, 128, 64, 10}; // Shallower network with ReLU
```

### 2. Data Leakage
```c
// Wrong: Normalize before train/test split
normalizeData(all_data, total_samples, num_features);
splitData(all_data, train_data, test_data); // Test data influences normalization

// Right: Split first, then normalize
splitData(all_data, train_data, test_data);
normalizeData(train_data, train_samples, num_features);
applyNormalization(test_data, train_mean, train_std);
```

### 3. Overfitting
```c
// Wrong: Train on entire dataset without validation
trainNeuralNetwork(network, all_data, all_targets, all_samples, 1000);

// Right: Use train/validation split
trainNeuralNetwork(network, train_data, train_targets, train_samples, 1000);
validateNeuralNetwork(network, val_data, val_targets, val_samples);
```

### 4. Improper Feature Scaling
```c
// Wrong: Different scales for features
features[0] = age;        // 0-100
features[1] = income;    // 0-1000000
features[2] = rating;    // 0-5

// Right: Normalize all features
normalizeFeatures(features, num_features);
```

## 🔧 Real-World Applications

### 1. Image Classification
```c
// CNN for image classification (simplified)
void classifyImage(double* image_pixels, int width, int height, NeuralNetwork* cnn) {
    // Preprocess image
    double* processed_image = preprocessImage(image_pixels, width, height);
    
    // Forward pass through CNN
    forwardPropagation(cnn, processed_image);
    
    // Get classification probabilities
    double* probabilities = getOutputLayer(cnn);
    
    // Find best class
    int best_class = argmax(probabilities, cnn->output_size);
    
    printf("Image classified as class %d with confidence %.2f\n", 
           best_class, probabilities[best_class]);
}
```

### 2. Time Series Prediction
```c
// LSTM for time series prediction (conceptual)
void predictTimeSeries(double* historical_data, int history_length, NeuralNetwork* lstm) {
    // Create sequences from historical data
    double** sequences = createSequences(historical_data, history_length);
    
    // Predict next value
    double prediction;
    forwardPropagation(lstm, sequences[0]);
    getOutput(lstm, &prediction);
    
    printf("Predicted next value: %.4f\n", prediction);
}
```

### 3. Anomaly Detection
```c
// Autoencoder for anomaly detection
int detectAnomaly(double* data_point, NeuralNetwork* autoencoder, double threshold) {
    // Encode and decode
    forwardPropagation(autoencoder, data_point);
    double* reconstruction = getOutput(autoencoder);
    
    // Calculate reconstruction error
    double error = calculateMeanSquaredError(data_point, reconstruction, feature_count);
    
    return error > threshold ? 1 : 0; // 1 = anomaly, 0 = normal
}
```

## 📚 Further Reading

### Books
- "Pattern Recognition and Machine Learning" by Christopher Bishop
- "Deep Learning" by Ian Goodfellow, Yoshua Bengio, and Aaron Courville
- "Machine Learning: A Probabilistic Perspective" by Kevin Murphy

### Topics
- Deep learning architectures (CNN, RNN, LSTM, Transformer)
- Reinforcement learning
- Natural language processing
- Computer vision
- Ensemble methods

Artificial intelligence algorithms in C provide the foundation for building intelligent systems that can learn from data, make predictions, and solve complex problems. Master these techniques to create sophisticated AI applications that can automate decision-making and discover patterns in data!
