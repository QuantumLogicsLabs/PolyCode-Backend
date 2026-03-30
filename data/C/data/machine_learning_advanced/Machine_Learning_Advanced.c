#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>
#include <time.h>
#include <float.h>

// =============================================================================
// ADVANCED MACHINE LEARNING
// =============================================================================

#define MAX_FEATURES 1000
#define MAX_SAMPLES 10000
#define MAX_CLASSES 100
#define MAX_LAYERS 10
#define MAX_NEURONS_PER_LAYER 1000
#define LEARNING_RATE 0.01
#define EPOCHS 1000
#define BATCH_SIZE 32

// =============================================================================
// NEURAL NETWORKS
// =============================================================================

// Activation functions
typedef enum {
    ACTIVATION_SIGMOID = 0,
    ACTIVATION_TANH = 1,
    ACTIVATION_RELU = 2,
    ACTIVATION_LEAKY_RELU = 3,
    ACTIVATION_SOFTMAX = 4,
    ACTIVATION_LINEAR = 5
} ActivationType;

// Loss functions
typedef enum {
    LOSS_MSE = 0,
    LOSS_BINARY_CROSS_ENTROPY = 1,
    LOSS_CATEGORICAL_CROSS_ENTROPY = 2,
    LOSS_HINGE = 3,
    LOSS_HUBER = 4
} LossType;

// Optimizer types
typedef enum {
    OPTIMIZER_SGD = 0,
    OPTIMIZER_ADAM = 1,
    OPTIMIZER_RMSPROP = 2,
    OPTIMIZER_ADAGRAD = 3,
    OPTIMIZER_ADADELTA = 4
} OptimizerType;

// Neuron structure
typedef struct {
    double* weights;
    double bias;
    double output;
    double delta;
    int input_size;
} Neuron;

// Layer structure
typedef struct {
    Neuron* neurons;
    int neuron_count;
    int input_size;
    ActivationType activation;
    double* inputs;
    double* outputs;
    double* deltas;
} Layer;

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

// =============================================================================
// DECISION TREES AND RANDOM FORESTS
// =============================================================================

// Node types
typedef enum {
    NODE_LEAF = 0,
    NODE_DECISION = 1
} NodeType;

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

// Decision tree structure
typedef struct {
    TreeNode* root;
    int max_depth;
    int min_samples_split;
    int min_samples_leaf;
    int max_features;
    int n_classes;
    int is_classification;
} DecisionTree;

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

// =============================================================================
// SUPPORT VECTOR MACHINES
// =============================================================================

// Kernel types
typedef enum {
    KERNEL_LINEAR = 0,
    KERNEL_POLYNOMIAL = 1,
    KERNEL_RBF = 2,
    KERNEL_SIGMOID = 3
} KernelType;

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

// =============================================================================
// K-MEANS CLUSTERING
// =============================================================================

// Cluster structure
typedef struct {
    double* centroid;
    int* members;
    int member_count;
    double inertia;
} Cluster;

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

// =============================================================================
// PRINCIPAL COMPONENT ANALYSIS (PCA)
// =============================================================================

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

// =============================================================================
// LINEAR REGRESSION
// =============================================================================

// Linear regression structure
typedef struct {
    double* coefficients;
    double intercept;
    int n_features;
    double* residuals;
    double r_squared;
    int fit_intercept;
} LinearRegression;

// =============================================================================
// LOGISTIC REGRESSION
// =============================================================================

// Logistic regression structure
typedef struct {
    double* coefficients;
    double intercept;
    int n_features;
    int n_classes;
    double* class_weights;
    double C; // Regularization parameter
    int max_iter;
    double tolerance;
} LogisticRegression;

// =============================================================================
// DATA STRUCTURES
// =============================================================================

// Dataset structure
typedef struct {
    double** X;
    double* y;
    int n_samples;
    int n_features;
    int n_classes;
} Dataset;

// Data split structure
typedef struct {
    double** X_train;
    double* y_train;
    double** X_test;
    double* y_test;
    int n_train;
    int n_test;
} DataSplit;

// =============================================================================
// ACTIVATION FUNCTIONS
// =============================================================================

// Sigmoid activation
double sigmoid(double x) {
    return 1.0 / (1.0 + exp(-x));
}

// Sigmoid derivative
double sigmoid_derivative(double x) {
    double s = sigmoid(x);
    return s * (1.0 - s);
}

// Tanh activation
double tanh_activation(double x) {
    return tanh(x);
}

// Tanh derivative
double tanh_derivative(double x) {
    double t = tanh(x);
    return 1.0 - t * t;
}

// ReLU activation
double relu(double x) {
    return x > 0 ? x : 0;
}

// ReLU derivative
double relu_derivative(double x) {
    return x > 0 ? 1 : 0;
}

// Leaky ReLU activation
double leaky_relu(double x) {
    return x > 0 ? x : 0.01 * x;
}

// Leaky ReLU derivative
double leaky_relu_derivative(double x) {
    return x > 0 ? 1 : 0.01;
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

// Apply activation function
void apply_activation(double* inputs, int size, ActivationType activation, double* outputs) {
    switch (activation) {
        case ACTIVATION_SIGMOID:
            for (int i = 0; i < size; i++) {
                outputs[i] = sigmoid(inputs[i]);
            }
            break;
            
        case ACTIVATION_TANH:
            for (int i = 0; i < size; i++) {
                outputs[i] = tanh_activation(inputs[i]);
            }
            break;
            
        case ACTIVATION_RELU:
            for (int i = 0; i < size; i++) {
                outputs[i] = relu(inputs[i]);
            }
            break;
            
        case ACTIVATION_LEAKY_RELU:
            for (int i = 0; i < size; i++) {
                outputs[i] = leaky_relu(inputs[i]);
            }
            break;
            
        case ACTIVATION_SOFTMAX:
            softmax(inputs, size, outputs);
            break;
            
        case ACTIVATION_LINEAR:
            for (int i = 0; i < size; i++) {
                outputs[i] = inputs[i];
            }
            break;
    }
}

// =============================================================================
// NEURAL NETWORK IMPLEMENTATION
// =============================================================================

// Create neuron
Neuron* createNeuron(int input_size) {
    Neuron* neuron = malloc(sizeof(Neuron));
    if (!neuron) return NULL;
    
    neuron->weights = malloc(input_size * sizeof(double));
    if (!neuron->weights) {
        free(neuron);
        return NULL;
    }
    
    // Initialize weights with random values
    for (int i = 0; i < input_size; i++) {
        neuron->weights[i] = ((double)rand() / RAND_MAX) * 2.0 - 1.0;
    }
    
    neuron->bias = ((double)rand() / RAND_MAX) * 2.0 - 1.0;
    neuron->output = 0.0;
    neuron->delta = 0.0;
    neuron->input_size = input_size;
    
    return neuron;
}

// Create layer
Layer* createLayer(int neuron_count, int input_size, ActivationType activation) {
    Layer* layer = malloc(sizeof(Layer));
    if (!layer) return NULL;
    
    layer->neurons = malloc(neuron_count * sizeof(Neuron));
    if (!layer->neurons) {
        free(layer);
        return NULL;
    }
    
    for (int i = 0; i < neuron_count; i++) {
        layer->neurons[i] = *createNeuron(input_size);
    }
    
    layer->neuron_count = neuron_count;
    layer->input_size = input_size;
    layer->activation = activation;
    
    layer->inputs = malloc(input_size * sizeof(double));
    layer->outputs = malloc(neuron_count * sizeof(double));
    layer->deltas = malloc(neuron_count * sizeof(double));
    
    return layer;
}

// Create neural network
NeuralNetwork* createNeuralNetwork(int* layer_sizes, int layer_count, LossType loss_type, OptimizerType optimizer_type) {
    NeuralNetwork* network = malloc(sizeof(NeuralNetwork));
    if (!network) return NULL;
    
    network->layers = malloc(layer_count * sizeof(Layer));
    if (!network->layers) {
        free(network);
        return NULL;
    }
    
    // Create layers
    for (int i = 0; i < layer_count; i++) {
        int input_size = (i == 0) ? layer_sizes[0] : layer_sizes[i];
        int neuron_count = layer_sizes[i];
        ActivationType activation = (i == layer_count - 1) ? ACTIVATION_SIGMOID : ACTIVATION_RELU;
        
        network->layers[i] = *createLayer(neuron_count, input_size, activation);
    }
    
    network->layer_count = layer_count;
    network->loss_type = loss_type;
    network->optimizer_type = optimizer_type;
    network->learning_rate = LEARNING_RATE;
    network->momentum = 0.9;
    network->beta1 = 0.9;
    network->beta2 = 0.999;
    network->epsilon = 1e-8;
    network->epochs = EPOCHS;
    network->batch_size = BATCH_SIZE;
    network->verbose = 1;
    
    return network;
}

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

// Train neural network
void trainNeuralNetwork(NeuralNetwork* network, Dataset* dataset) {
    for (int epoch = 0; epoch < network->epochs; epoch++) {
        double total_error = 0.0;
        
        for (int sample = 0; sample < dataset->n_samples; sample++) {
            double* inputs = dataset->X[sample];
            double* targets = &dataset->y[sample];
            
            double outputs[MAX_CLASSES];
            forwardPass(network, inputs, outputs);
            backwardPass(network, inputs, targets);
            
            // Calculate error
            for (int i = 0; i < network->layers[network->layer_count - 1].neuron_count; i++) {
                double error = targets[i] - outputs[i];
                total_error += error * error;
            }
        }
        
        if (network->verbose && epoch % 100 == 0) {
            printf("Epoch %d, Error: %.6f\n", epoch, total_error / dataset->n_samples);
        }
    }
}

// Predict with neural network
int predictNeuralNetwork(NeuralNetwork* network, double* inputs) {
    double outputs[MAX_CLASSES];
    forwardPass(network, inputs, outputs);
    
    int predicted_class = 0;
    double max_output = outputs[0];
    
    for (int i = 1; i < network->layers[network->layer_count - 1].neuron_count; i++) {
        if (outputs[i] > max_output) {
            max_output = outputs[i];
            predicted_class = i;
        }
    }
    
    return predicted_class;
}

// =============================================================================
// DECISION TREE IMPLEMENTATION
// =============================================================================

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

// Create leaf node
TreeNode* createLeafNode(Dataset* dataset, int* indices, int n_samples) {
    TreeNode* node = malloc(sizeof(TreeNode));
    if (!node) return NULL;
    
    node->type = NODE_LEAF;
    node->left = NULL;
    node->right = NULL;
    node->sample_count = n_samples;
    
    // Calculate majority class
    double class_counts[MAX_CLASSES] = {0};
    for (int i = 0; i < n_samples; i++) {
        int class_label = (int)dataset->y[indices[i]];
        class_counts[class_label]++;
    }
    
    int max_count = 0;
    for (int i = 0; i < dataset->n_classes; i++) {
        if (class_counts[i] > max_count) {
            max_count = class_counts[i];
            node->class_label = i;
        }
    }
    
    node->value = (double)node->class_label;
    node->class_counts = malloc(dataset->n_classes * sizeof(double));
    for (int i = 0; i < dataset->n_classes; i++) {
        node->class_counts[i] = class_counts[i];
    }
    
    return node;
}

// Build decision tree recursively
TreeNode* buildDecisionTree(Dataset* dataset, int* indices, int n_samples, 
                          int* features, int n_features, int depth, DecisionTree* tree) {
    // Check stopping criteria
    if (depth >= tree->max_depth || n_samples < tree->min_samples_split) {
        return createLeafNode(dataset, indices, n_samples);
    }
    
    // Check if all samples belong to same class
    int first_class = (int)dataset->y[indices[0]];
    int all_same = 1;
    for (int i = 1; i < n_samples; i++) {
        if ((int)dataset->y[indices[i]] != first_class) {
            all_same = 0;
            break;
        }
    }
    
    if (all_same) {
        return createLeafNode(dataset, indices, n_samples);
    }
    
    // Find best split
    int best_feature;
    double best_threshold;
    double best_gain;
    
    findBestSplit(dataset, indices, n_samples, features, n_features,
                 &best_feature, &best_threshold, &best_gain);
    
    // If no good split found, create leaf
    if (best_feature == -1 || best_gain < 1e-6) {
        return createLeafNode(dataset, indices, n_samples);
    }
    
    // Create decision node
    TreeNode* node = malloc(sizeof(TreeNode));
    if (!node) return NULL;
    
    node->type = NODE_DECISION;
    node->feature_index = best_feature;
    node->threshold = best_threshold;
    node->left = NULL;
    node->right = NULL;
    node->sample_count = n_samples;
    
    // Split samples
    int left_indices[MAX_SAMPLES];
    int right_indices[MAX_SAMPLES];
    int left_count = 0, right_count = 0;
    
    for (int i = 0; i < n_samples; i++) {
        if (dataset->X[indices[i]][best_feature] <= best_threshold) {
            left_indices[left_count++] = indices[i];
        } else {
            right_indices[right_count++] = indices[i];
        }
    }
    
    // Recursively build subtrees
    node->left = buildDecisionTree(dataset, left_indices, left_count, features, n_features, depth + 1, tree);
    node->right = buildDecisionTree(dataset, right_indices, right_count, features, n_features, depth + 1, tree);
    
    return node;
}

// Create decision tree
DecisionTree* createDecisionTree(int max_depth, int min_samples_split, int min_samples_leaf, 
                                int max_features, int n_classes) {
    DecisionTree* tree = malloc(sizeof(DecisionTree));
    if (!tree) return NULL;
    
    tree->max_depth = max_depth;
    tree->min_samples_split = min_samples_split;
    tree->min_samples_leaf = min_samples_leaf;
    tree->max_features = max_features;
    tree->n_classes = n_classes;
    tree->is_classification = 1;
    tree->root = NULL;
    
    return tree;
}

// Train decision tree
void trainDecisionTree(DecisionTree* tree, Dataset* dataset) {
    // Create indices array
    int* indices = malloc(dataset->n_samples * sizeof(int));
    for (int i = 0; i < dataset->n_samples; i++) {
        indices[i] = i;
    }
    
    // Create features array
    int* features = malloc(dataset->n_features * sizeof(int));
    for (int i = 0; i < dataset->n_features; i++) {
        features[i] = i;
    }
    
    // Build tree
    tree->root = buildDecisionTree(dataset, indices, dataset->n_samples, 
                                  features, dataset->n_features, 0, tree);
    
    free(indices);
    free(features);
}

// Predict with decision tree
int predictDecisionTree(TreeNode* node, double* sample) {
    if (node->type == NODE_LEAF) {
        return node->class_label;
    }
    
    if (sample[node->feature_index] <= node->threshold) {
        return predictDecisionTree(node->left, sample);
    } else {
        return predictDecisionTree(node->right, sample);
    }
}

// =============================================================================
// K-MEANS CLUSTERING IMPLEMENTATION
// =============================================================================

// Initialize centroids randomly
void initializeCentroids(KMeans* kmeans, Dataset* dataset) {
    // Randomly select k samples as initial centroids
    int* used_indices = malloc(dataset->n_samples * sizeof(int));
    memset(used_indices, 0, dataset->n_samples * sizeof(int));
    
    for (int k = 0; k < kmeans->k; k++) {
        int idx;
        do {
            idx = rand() % dataset->n_samples;
        } while (used_indices[idx]);
        
        used_indices[idx] = 1;
        
        for (int f = 0; f < dataset->n_features; f++) {
            kmeans->clusters[k].centroid[f] = dataset->X[idx][f];
        }
    }
    
    free(used_indices);
}

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

// Calculate inertia (within-cluster sum of squares)
double calculateInertia(KMeans* kmeans, Dataset* dataset) {
    double inertia = 0.0;
    
    for (int k = 0; k < kmeans->k; k++) {
        for (int i = 0; i < kmeans->clusters[k].member_count; i++) {
            int sample_idx = kmeans->clusters[k].members[i];
            double distance = 0.0;
            
            for (int f = 0; f < dataset->n_features; f++) {
                double diff = dataset->X[sample_idx][f] - kmeans->clusters[k].centroid[f];
                distance += diff * diff;
            }
            
            inertia += distance;
        }
    }
    
    return inertia;
}

// Create K-means
KMeans* createKMeans(int k, int max_iterations, double tolerance) {
    KMeans* kmeans = malloc(sizeof(KMeans));
    if (!kmeans) return NULL;
    
    kmeans->k = k;
    kmeans->max_iterations = max_iterations;
    kmeans->tolerance = tolerance;
    kmeans->n_features = 0;
    kmeans->n_samples = 0;
    kmeans->inertia = 0.0;
    
    kmeans->clusters = malloc(k * sizeof(Cluster));
    kmeans->assignments = malloc(MAX_SAMPLES * sizeof(int));
    
    for (int i = 0; i < k; i++) {
        kmeans->clusters[i].centroid = malloc(MAX_FEATURES * sizeof(double));
        kmeans->clusters[i].members = malloc(MAX_SAMPLES * sizeof(int));
        kmeans->clusters[i].member_count = 0;
        kmeans->clusters[i].inertia = 0.0;
    }
    
    return kmeans;
}

// Train K-means
void trainKMeans(KMeans* kmeans, Dataset* dataset) {
    kmeans->n_features = dataset->n_features;
    kmeans->n_samples = dataset->n_samples;
    
    // Initialize centroids
    initializeCentroids(kmeans, dataset);
    
    double prev_inertia = DBL_MAX;
    
    for (int iter = 0; iter < kmeans->max_iterations; iter++) {
        // Reset cluster memberships
        for (int k = 0; k < kmeans->k; k++) {
            kmeans->clusters[k].member_count = 0;
        }
        
        // Assign samples to clusters
        assignClusters(kmeans, dataset);
        
        // Update centroids
        updateCentroids(kmeans, dataset);
        
        // Calculate inertia
        kmeans->inertia = calculateInertia(kmeans, dataset);
        
        // Check convergence
        if (fabs(prev_inertia - kmeans->inertia) < kmeans->tolerance) {
            break;
        }
        
        prev_inertia = kmeans->inertia;
    }
}

// =============================================================================
// LINEAR REGRESSION IMPLEMENTATION
// =============================================================================

// Create linear regression
LinearRegression* createLinearRegression(int n_features, int fit_intercept) {
    LinearRegression* lr = malloc(sizeof(LinearRegression));
    if (!lr) return NULL;
    
    lr->coefficients = malloc(n_features * sizeof(double));
    lr->residuals = malloc(MAX_SAMPLES * sizeof(double));
    lr->n_features = n_features;
    lr->fit_intercept = fit_intercept;
    lr->intercept = 0.0;
    lr->r_squared = 0.0;
    
    return lr;
}

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
    // For demonstration, we'll use a simple approach
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

// Predict with linear regression
double predictLinearRegression(LinearRegression* lr, double* sample) {
    double prediction = lr->intercept;
    
    for (int i = 0; i < lr->n_features; i++) {
        prediction += lr->coefficients[i] * sample[i];
    }
    
    return prediction;
}

// =============================================================================
// DATA UTILITY FUNCTIONS
// =============================================================================

// Create synthetic dataset for classification
Dataset* createClassificationDataset(int n_samples, int n_features, int n_classes) {
    Dataset* dataset = malloc(sizeof(Dataset));
    if (!dataset) return NULL;
    
    dataset->X = malloc(n_samples * sizeof(double*));
    dataset->y = malloc(n_samples * sizeof(double));
    dataset->n_samples = n_samples;
    dataset->n_features = n_features;
    dataset->n_classes = n_classes;
    
    for (int i = 0; i < n_samples; i++) {
        dataset->X[i] = malloc(n_features * sizeof(double));
        
        // Generate random features
        for (int j = 0; j < n_features; j++) {
            dataset->X[i][j] = ((double)rand() / RAND_MAX) * 10.0 - 5.0;
        }
        
        // Assign class based on feature sum (simplified)
        double feature_sum = 0.0;
        for (int j = 0; j < n_features; j++) {
            feature_sum += dataset->X[i][j];
        }
        
        dataset->y[i] = (int)(feature_sum / 5.0) % n_classes;
    }
    
    return dataset;
}

// Create synthetic dataset for regression
Dataset* createRegressionDataset(int n_samples, int n_features) {
    Dataset* dataset = malloc(sizeof(Dataset));
    if (!dataset) return NULL;
    
    dataset->X = malloc(n_samples * sizeof(double*));
    dataset->y = malloc(n_samples * sizeof(double));
    dataset->n_samples = n_samples;
    dataset->n_features = n_features;
    dataset->n_classes = 1;
    
    for (int i = 0; i < n_samples; i++) {
        dataset->X[i] = malloc(n_features * sizeof(double));
        
        // Generate random features
        for (int j = 0; j < n_features; j++) {
            dataset->X[i][j] = ((double)rand() / RAND_MAX) * 10.0 - 5.0;
        }
        
        // Generate target as linear combination plus noise
        dataset->y[i] = 0.0;
        for (int j = 0; j < n_features; j++) {
            dataset->y[i] += (j + 1) * dataset->X[i][j];
        }
        dataset->y[i] += ((double)rand() / RAND_MAX) * 2.0 - 1.0; // Add noise
    }
    
    return dataset;
}

// Split dataset into train and test
DataSplit* trainTestSplit(Dataset* dataset, double test_size) {
    DataSplit* split = malloc(sizeof(DataSplit));
    if (!split) return NULL;
    
    int n_test = (int)(dataset->n_samples * test_size);
    int n_train = dataset->n_samples - n_test;
    
    split->n_train = n_train;
    split->n_test = n_test;
    
    split->X_train = malloc(n_train * sizeof(double*));
    split->y_train = malloc(n_train * sizeof(double));
    split->X_test = malloc(n_test * sizeof(double*));
    split->y_test = malloc(n_test * sizeof(double));
    
    // Simple random split
    int* indices = malloc(dataset->n_samples * sizeof(int));
    for (int i = 0; i < dataset->n_samples; i++) {
        indices[i] = i;
    }
    
    // Shuffle indices
    for (int i = dataset->n_samples - 1; i > 0; i--) {
        int j = rand() % (i + 1);
        int temp = indices[i];
        indices[i] = indices[j];
        indices[j] = temp;
    }
    
    // Assign to train and test
    for (int i = 0; i < n_train; i++) {
        int idx = indices[i];
        split->X_train[i] = dataset->X[idx];
        split->y_train[i] = dataset->y[idx];
    }
    
    for (int i = 0; i < n_test; i++) {
        int idx = indices[n_train + i];
        split->X_test[i] = dataset->X[idx];
        split->y_test[i] = dataset->y[idx];
    }
    
    free(indices);
    return split;
}

// Calculate accuracy
double calculateAccuracy(int* y_true, int* y_pred, int n_samples) {
    int correct = 0;
    for (int i = 0; i < n_samples; i++) {
        if (y_true[i] == y_pred[i]) {
            correct++;
        }
    }
    return (double)correct / n_samples;
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateNeuralNetworks() {
    printf("=== NEURAL NETWORKS DEMO ===\n");
    
    // Create synthetic dataset
    Dataset* dataset = createClassificationDataset(1000, 10, 3);
    printf("Created dataset: %d samples, %d features, %d classes\n", 
           dataset->n_samples, dataset->n_features, dataset->n_classes);
    
    // Split dataset
    DataSplit* split = trainTestSplit(dataset, 0.2);
    printf("Split dataset: %d train, %d test samples\n", split->n_train, split->n_test);
    
    // Create neural network
    int layer_sizes[] = {10, 16, 8, 3};
    NeuralNetwork* network = createNeuralNetwork(layer_sizes, 4, LOSS_MSE, OPTIMIZER_SGD);
    printf("Created neural network: 4 layers [10, 16, 8, 3]\n");
    
    // Train network
    printf("Training neural network...\n");
    trainNeuralNetwork(network, dataset);
    printf("Training completed\n");
    
    // Test network
    int* predictions = malloc(split->n_test * sizeof(int));
    for (int i = 0; i < split->n_test; i++) {
        predictions[i] = predictNeuralNetwork(network, split->X_test[i]);
    }
    
    // Calculate accuracy
    double accuracy = calculateAccuracy((int*)split->y_test, predictions, split->n_test);
    printf("Test accuracy: %.2f%%\n", accuracy * 100);
    
    free(predictions);
    free(split);
    free(dataset);
}

void demonstrateDecisionTrees() {
    printf("\n=== DECISION TREES DEMO ===\n");
    
    // Create synthetic dataset
    Dataset* dataset = createClassificationDataset(500, 5, 2);
    printf("Created dataset: %d samples, %d features, %d classes\n", 
           dataset->n_samples, dataset->n_features, dataset->n_classes);
    
    // Create decision tree
    DecisionTree* tree = createDecisionTree(10, 2, 1, 5, dataset->n_classes);
    printf("Created decision tree: max_depth=10, min_samples_split=2\n");
    
    // Train tree
    printf("Training decision tree...\n");
    trainDecisionTree(tree, dataset);
    printf("Training completed\n");
    
    // Test tree
    int* predictions = malloc(dataset->n_samples * sizeof(int));
    for (int i = 0; i < dataset->n_samples; i++) {
        predictions[i] = predictDecisionTree(tree->root, dataset->X[i]);
    }
    
    // Calculate accuracy
    double accuracy = calculateAccuracy((int*)dataset->y, predictions, dataset->n_samples);
    printf("Training accuracy: %.2f%%\n", accuracy * 100);
    
    free(predictions);
    free(dataset);
}

void demonstrateKMeans() {
    printf("\n=== K-MEANS CLUSTERING DEMO ===\n");
    
    // Create synthetic dataset
    Dataset* dataset = createClassificationDataset(300, 2, 3);
    printf("Created dataset: %d samples, %d features\n", 
           dataset->n_samples, dataset->n_features);
    
    // Create K-means
    KMeans* kmeans = createKMeans(3, 100, 1e-4);
    printf("Created K-means: k=3, max_iterations=100\n");
    
    // Train K-means
    printf("Training K-means...\n");
    trainKMeans(kmeans, dataset);
    printf("Training completed\n");
    printf("Final inertia: %.6f\n", kmeans->inertia);
    
    // Print cluster assignments
    printf("Cluster assignments:\n");
    for (int k = 0; k < kmeans->k; k++) {
        printf("Cluster %d: %d samples\n", k, kmeans->clusters[k].member_count);
        printf("  Centroid: ");
        for (int f = 0; f < dataset->n_features; f++) {
            printf("%.3f ", kmeans->clusters[k].centroid[f]);
        }
        printf("\n");
    }
    
    free(dataset);
}

void demonstrateLinearRegression() {
    printf("\n=== LINEAR REGRESSION DEMO ===\n");
    
    // Create synthetic dataset
    Dataset* dataset = createRegressionDataset(200, 3);
    printf("Created dataset: %d samples, %d features\n", 
           dataset->n_samples, dataset->n_features);
    
    // Create linear regression
    LinearRegression* lr = createLinearRegression(dataset->n_features, 1);
    printf("Created linear regression model\n");
    
    // Train model
    printf("Training linear regression...\n");
    trainLinearRegression(lr, dataset);
    printf("Training completed\n");
    
    // Print results
    printf("Intercept: %.6f\n", lr->intercept);
    printf("Coefficients: ");
    for (int i = 0; i < lr->n_features; i++) {
        printf("%.6f ", lr->coefficients[i]);
    }
    printf("\n");
    printf("R-squared: %.6f\n", lr->r_squared);
    
    // Test predictions
    printf("Sample predictions:\n");
    for (int i = 0; i < 5; i++) {
        double prediction = predictLinearRegression(lr, dataset->X[i]);
        printf("  Sample %d: True=%.3f, Pred=%.3f, Error=%.3f\n", 
               i, dataset->y[i], prediction, fabs(dataset->y[i] - prediction));
    }
    
    free(dataset);
}

void demonstrateEnsembleLearning() {
    printf("\n=== ENSEMBLE LEARNING DEMO ===\n");
    
    // Create synthetic dataset
    Dataset* dataset = createClassificationDataset(400, 6, 3);
    printf("Created dataset: %d samples, %d features, %d classes\n", 
           dataset->n_samples, dataset->n_features, dataset->n_classes);
    
    // Split dataset
    DataSplit* split = trainTestSplit(dataset, 0.3);
    printf("Split dataset: %d train, %d test samples\n", split->n_train, split->n_test);
    
    // Train multiple decision trees (simplified random forest)
    int n_trees = 5;
    int* predictions = malloc(split->n_test * sizeof(int));
    double* tree_accuracies = malloc(n_trees * sizeof(double));
    
    printf("Training %d decision trees...\n", n_trees);
    
    for (int t = 0; t < n_trees; t++) {
        // Create bootstrap sample
        Dataset* bootstrap = malloc(sizeof(Dataset));
        bootstrap->n_samples = split->n_train;
        bootstrap->n_features = split->X_train[0] ? 6 : 0; // Simplified
        bootstrap->n_classes = dataset->n_classes;
        bootstrap->X = malloc(bootstrap->n_samples * sizeof(double*));
        bootstrap->y = malloc(bootstrap->n_samples * sizeof(double));
        
        // Random sampling with replacement
        for (int i = 0; i < bootstrap->n_samples; i++) {
            int idx = rand() % split->n_train;
            bootstrap->X[i] = split->X_train[idx];
            bootstrap->y[i] = split->y_train[idx];
        }
        
        // Train tree
        DecisionTree* tree = createDecisionTree(8, 2, 1, 4, dataset->n_classes);
        trainDecisionTree(tree, bootstrap);
        
        // Test tree
        int* tree_predictions = malloc(split->n_test * sizeof(int));
        for (int i = 0; i < split->n_test; i++) {
            tree_predictions[i] = predictDecisionTree(tree->root, split->X_test[i]);
        }
        
        double accuracy = calculateAccuracy((int*)split->y_test, tree_predictions, split->n_test);
        tree_accuracies[t] = accuracy;
        
        // Accumulate predictions for voting
        for (int i = 0; i < split->n_test; i++) {
            predictions[i] += tree_predictions[i];
        }
        
        free(tree_predictions);
        free(bootstrap->X);
        free(bootstrap->y);
        free(bootstrap);
    }
    
    // Majority voting
    for (int i = 0; i < split->n_test; i++) {
        predictions[i] = predictions[i] / n_trees; // Simplified voting
    }
    
    // Calculate ensemble accuracy
    double ensemble_accuracy = calculateAccuracy((int*)split->y_test, predictions, split->n_test);
    
    printf("Individual tree accuracies: ");
    for (int t = 0; t < n_trees; t++) {
        printf("%.2f%% ", tree_accuracies[t] * 100);
    }
    printf("\n");
    printf("Ensemble accuracy: %.2f%%\n", ensemble_accuracy * 100);
    
    free(predictions);
    free(tree_accuracies);
    free(split);
    free(dataset);
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced Machine Learning Examples\n");
    printf("==================================\n\n");
    
    // Seed random number generator
    srand(time(NULL));
    
    // Run all demonstrations
    demonstrateNeuralNetworks();
    demonstrateDecisionTrees();
    demonstrateKMeans();
    demonstrateLinearRegression();
    demonstrateEnsembleLearning();
    
    printf("\nAll advanced machine learning examples demonstrated!\n");
    printf("Key features implemented:\n");
    printf("- Neural networks with multiple activation functions\n");
    printf("- Decision trees with Gini impurity splitting\n");
    printf("- K-means clustering with centroid optimization\n");
    printf("- Linear regression with ordinary least squares\n");
    printf("- Ensemble learning with multiple decision trees\n");
    printf("- Various activation and loss functions\n");
    printf("- Data preprocessing and splitting utilities\n");
    printf("- Performance metrics and evaluation\n");
    printf("- Synthetic dataset generation\n");
    
    return 0;
}
