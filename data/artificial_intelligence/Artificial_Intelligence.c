#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>
#include <time.h>
#include <float.h>

// =============================================================================
// ARTIFICIAL INTELLIGENCE ALGORITHMS
// =============================================================================

#define MAX_FEATURES 100
#define MAX_SAMPLES 1000
#define MAX_NEURONS 1000
#define MAX_LAYERS 10
#define LEARNING_RATE 0.01
#define EPOCHS 1000
#define HIDDEN_NEURONS 64
#define INPUT_NEURONS 784 // 28x28 images

// =============================================================================
// NEURAL NETWORKS
// =============================================================================

// Activation functions
typedef enum {
    ACTIVATION_SIGMOID = 0,
    ACTIVATION_TANH = 1,
    ACTIVATION_RELU = 2,
    ACTIVATION_SOFTMAX = 3
} ActivationFunction;

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

// =============================================================================
// ACTIVATION FUNCTIONS
// =============================================================================

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

double softmax(double* inputs, int count, int index) {
    double sum = 0.0;
    double max_input = inputs[0];
    
    // Find maximum input for numerical stability
    for (int i = 1; i < count; i++) {
        if (inputs[i] > max_input) {
            max_input = inputs[i];
        }
    }
    
    // Calculate exponential sum
    for (int i = 0; i < count; i++) {
        sum += exp(inputs[i] - max_input);
    }
    
    return exp(inputs[index] - max_input) / sum;
}

// =============================================================================
// NEURAL NETWORK IMPLEMENTATION
// =============================================================================

// Create neuron
Neuron* createNeuron(int input_count, ActivationFunction activation) {
    Neuron* neuron = malloc(sizeof(Neuron));
    if (!neuron) return NULL;
    
    neuron->weights = malloc(input_count * sizeof(double));
    if (!neuron->weights) {
        free(neuron);
        return NULL;
    }
    
    // Initialize weights with random values
    for (int i = 0; i < input_count; i++) {
        neuron->weights[i] = ((double)rand() / RAND_MAX) * 2.0 - 1.0;
    }
    
    neuron->bias = ((double)rand() / RAND_MAX) * 2.0 - 1.0;
    neuron->output = 0.0;
    neuron->delta = 0.0;
    neuron->input_count = input_count;
    neuron->activation = activation;
    
    return neuron;
}

// Create layer
Layer* createLayer(int neuron_count, int input_count, ActivationFunction activation) {
    Layer* layer = malloc(sizeof(Layer));
    if (!layer) return NULL;
    
    layer->neurons = malloc(neuron_count * sizeof(Neuron));
    layer->outputs = malloc(neuron_count * sizeof(double));
    layer->deltas = malloc(neuron_count * sizeof(double));
    
    if (!layer->neurons || !layer->outputs || !layer->deltas) {
        free(layer->neurons);
        free(layer->outputs);
        free(layer->deltas);
        free(layer);
        return NULL;
    }
    
    for (int i = 0; i < neuron_count; i++) {
        layer->neurons[i] = *createNeuron(input_count, activation);
        layer->outputs[i] = 0.0;
        layer->deltas[i] = 0.0;
    }
    
    layer->neuron_count = neuron_count;
    
    return layer;
}

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

// Train neural network
void trainNeuralNetwork(NeuralNetwork* network, double** training_inputs, 
                        double** training_targets, int training_count, int epochs) {
    for (int epoch = 0; epoch < epochs; epoch++) {
        double total_loss = 0.0;
        
        for (int sample = 0; sample < training_count; sample++) {
            // Forward propagation
            forwardPropagation(network, training_inputs[sample]);
            
            // Calculate loss (mean squared error)
            Layer* output_layer = &network->layers[network->layer_count - 1];
            double sample_loss = 0.0;
            
            for (int n = 0; n < output_layer->neuron_count; n++) {
                double output = output_layer->outputs[n];
                double target = training_targets[sample][n];
                double error = target - output;
                sample_loss += error * error;
            }
            
            total_loss += sample_loss / output_layer->neuron_count;
            
            // Backward propagation
            backwardPropagation(network, training_targets[sample]);
        }
        
        network->training_loss = total_loss / training_count;
        network->epoch_count++;
        
        if (epoch % 100 == 0) {
            printf("Epoch %d, Loss: %.6f\n", epoch, network->training_loss);
        }
    }
}

// Predict with neural network
void predictNeuralNetwork(NeuralNetwork* network, double* inputs, double* outputs) {
    forwardPropagation(network, inputs);
    
    Layer* output_layer = &network->layers[network->layer_count - 1];
    for (int i = 0; i < output_layer->neuron_count; i++) {
        outputs[i] = output_layer->outputs[i];
    }
}

// =============================================================================
// DECISION TREES
// =============================================================================

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

// Create decision tree
DecisionTree* createDecisionTree(int max_depth, int min_samples_split) {
    DecisionTree* tree = malloc(sizeof(DecisionTree));
    if (!tree) return NULL;
    
    tree->max_depth = max_depth;
    tree->min_samples_split = min_samples_split;
    tree->root = NULL;
    
    return tree;
}

// Train decision tree
void trainDecisionTree(DecisionTree* tree, double** features, int* labels, 
                       int num_samples, int num_features) {
    tree->root = buildDecisionTree(features, labels, num_samples, num_features, 0);
}

// Predict with decision tree
int predictDecisionTree(DecisionTree* tree, double* features) {
    DecisionTreeNode* node = tree->root;
    
    while (node && !node->is_leaf) {
        if (features[node->feature_index] <= node->threshold) {
            node = node->left;
        } else {
            node = node->right;
        }
    }
    
    return node ? node->class_label : -1;
}

// =============================================================================
// K-MEANS CLUSTERING
// =============================================================================

// Cluster structure
typedef struct {
    double* centroid;
    int* samples;
    int sample_count;
    int cluster_id;
} Cluster;

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

// =============================================================================
// GENETIC ALGORITHM
// =============================================================================

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

// Fitness function example (sphere function)
double fitnessFunction(double* genes, int gene_count) {
    double sum = 0.0;
    for (int i = 0; i < gene_count; i++) {
        sum += genes[i] * genes[i];
    }
    return 1.0 / (1.0 + sum); // Minimize sum of squares
}

// Create individual
Individual* createIndividual(int gene_count) {
    Individual* individual = malloc(sizeof(Individual));
    if (!individual) return NULL;
    
    individual->genes = malloc(gene_count * sizeof(double));
    individual->fitness = 0.0;
    individual->gene_count = gene_count;
    
    // Initialize with random genes
    for (int i = 0; i < gene_count; i++) {
        individual->genes[i] = ((double)rand() / RAND_MAX) * 10.0 - 5.0; // Range [-5, 5]
    }
    
    return individual;
}

// Create population
Population* createPopulation(int population_size, int gene_count, 
                          double mutation_rate, double crossover_rate) {
    Population* population = malloc(sizeof(Population));
    if (!population) return NULL;
    
    population->individuals = malloc(population_size * sizeof(Individual));
    population->population_size = population_size;
    population->gene_count = gene_count;
    population->mutation_rate = mutation_rate;
    population->crossover_rate = crossover_rate;
    
    for (int i = 0; i < population_size; i++) {
        population->individuals[i] = *createIndividual(gene_count);
        population->individuals[i].fitness = fitnessFunction(population->individuals[i].genes, gene_count);
    }
    
    return population;
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

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateNeuralNetwork() {
    printf("=== NEURAL NETWORK DEMO ===\n");
    
    // Create a simple neural network for XOR problem
    int layer_sizes[] = {2, 4, 1}; // 2 inputs, 4 hidden, 1 output
    NeuralNetwork* nn = createNeuralNetwork(layer_sizes, 3, ACTIVATION_SIGMOID);
    
    // XOR training data
    double inputs[4][2] = {{0, 0}, {0, 1}, {1, 0}, {1, 1}};
    double targets[4][1] = {{0}, {1}, {1}, {0}};
    double* input_ptrs[4] = {inputs[0], inputs[1], inputs[2], inputs[3]};
    double* target_ptrs[4] = {targets[0], targets[1], targets[2], targets[3]};
    
    printf("Training neural network for XOR problem...\n");
    trainNeuralNetwork(nn, input_ptrs, target_ptrs, 4, 1000);
    
    // Test the network
    printf("\nTesting neural network:\n");
    for (int i = 0; i < 4; i++) {
        double output;
        predictNeuralNetwork(nn, input_ptrs[i], &output);
        printf("Input: (%.1f, %.1f), Target: %.1f, Output: %.6f\n", 
               inputs[i][0], inputs[i][1], targets[i][0][0], output);
    }
    
    // Clean up
    for (int i = 0; i < nn->layer_count; i++) {
        free(nn->layers[i].neurons);
        free(nn->layers[i].outputs);
        free(nn->layers[i].deltas);
    }
    free(nn->layers);
    free(nn);
}

void demonstrateDecisionTree() {
    printf("\n=== DECISION TREE DEMO ===\n");
    
    // Create sample data for classification
    double features[10][2] = {
        {1.0, 1.0}, {1.0, 2.0}, {1.0, 3.0}, {2.0, 1.0}, {2.0, 2.0},
        {3.0, 1.0}, {3.0, 2.0}, {3.0, 3.0}, {4.0, 1.0}, {4.0, 2.0}
    };
    int labels[10] = {0, 0, 1, 0, 0, 1, 1, 1, 1, 1};
    
    double* feature_ptrs[10];
    for (int i = 0; i < 10; i++) {
        feature_ptrs[i] = features[i];
    }
    
    // Create and train decision tree
    DecisionTree* tree = createDecisionTree(5, 2);
    trainDecisionTree(tree, feature_ptrs, labels, 10, 2);
    
    // Test the tree
    printf("Testing decision tree:\n");
    double test_cases[3][2] = {{1.5, 1.5}, {2.5, 2.5}, {3.5, 2.5}};
    
    for (int i = 0; i < 3; i++) {
        int prediction = predictDecisionTree(tree, test_cases[i]);
        printf("Input: (%.1f, %.1f), Prediction: %d\n", 
               test_cases[i][0], test_cases[i][1], prediction);
    }
    
    // Clean up
    free(tree->root);
    free(tree);
}

void demonstrateKMeans() {
    printf("\n=== K-MEANS CLUSTERING DEMO ===\n");
    
    // Create sample data
    double data[100][2];
    int num_samples = 100;
    
    // Generate three clusters
    for (int i = 0; i < 33; i++) {
        data[i][0] = ((double)rand() / RAND_MAX) * 2.0 - 1.0; // Around (0, 0)
        data[i][1] = ((double)rand() / RAND_MAX) * 2.0 - 1.0;
    }
    for (int i = 33; i < 66; i++) {
        data[i][0] = ((double)rand() / RAND_MAX) * 2.0 + 2.0; // Around (3, 0)
        data[i][1] = ((double)rand() / RAND_MAX) * 2.0 - 1.0;
    }
    for (int i = 66; i < 100; i++) {
        data[i][0] = ((double)rand() / RAND_MAX) * 2.0 + 0.5; // Around (1.5, 3)
        data[i][1] = ((double)rand() / RAND_MAX) * 2.0 + 2.0;
    }
    
    double* data_ptrs[100];
    for (int i = 0; i < 100; i++) {
        data_ptrs[i] = data[i];
    }
    
    // Perform K-means clustering
    int k = 3;
    Cluster* clusters = kMeansClustering(data_ptrs, num_samples, 2, k, 50);
    
    printf("K-means clustering completed with %d clusters:\n", k);
    for (int i = 0; i < k; i++) {
        printf("Cluster %d: Centroid (%.2f, %.2f), Size: %d\n", 
               i, clusters[i].centroid[0], clusters[i].centroid[1], clusters[i].sample_count);
    }
    
    // Clean up
    for (int i = 0; i < k; i++) {
        free(clusters[i].centroid);
        free(clusters[i].samples);
    }
    free(clusters);
}

void demonstrateGeneticAlgorithm() {
    printf("\n=== GENETIC ALGORITHM DEMO ===\n");
    
    // Create population
    int population_size = 50;
    int gene_count = 10;
    double mutation_rate = 0.01;
    double crossover_rate = 0.8;
    
    Population* population = createPopulation(population_size, gene_count, 
                                            mutation_rate, crossover_rate);
    
    printf("Evolving population to minimize sum of squares...\n");
    evolvePopulation(population, 100);
    
    // Show best solution
    Individual* best = selection(population, population_size);
    printf("Best solution found:\n");
    for (int i = 0; i < gene_count; i++) {
        printf("Gene %d: %.4f\n", i, best->genes[i]);
    }
    printf("Fitness: %.6f\n", best->fitness);
    
    // Clean up
    for (int i = 0; i < population_size; i++) {
        free(population->individuals[i].genes);
    }
    free(population->individuals);
    free(population);
}

void demonstrateLinearRegression() {
    printf("\n=== LINEAR REGRESSION DEMO ===\n");
    
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

void demonstratePerceptron() {
    printf("\n=== PERCEPTRON DEMO ===\n");
    
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
    
    // Test the perceptron
    printf("\nTesting perceptron:\n");
    double test_cases[4][2] = {{-1, -1}, {1, 1}, {-1, 1}, {1, -1}};
    
    for (int i = 0; i < 4; i++) {
        double activation = weights[0] + weights[1] * test_cases[i][0] + weights[2] * test_cases[i][1];
        int prediction = (activation > 0) ? 1 : 0;
        printf("Input: (%.1f, %.1f), Prediction: %d\n", 
               test_cases[i][0], test_cases[i][1], prediction);
    }
}

void demonstratePrincipalComponentAnalysis() {
    printf("\n=== PRINCIPAL COMPONENT ANALYSIS DEMO ===\n");
    
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

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Artificial Intelligence Algorithms Examples\n");
    printf("======================================\n\n");
    
    // Seed random number generator
    srand(time(NULL));
    
    // Run all demonstrations
    demonstrateNeuralNetwork();
    demonstrateDecisionTree();
    demonstrateKMeans();
    demonstrateGeneticAlgorithm();
    demonstrateLinearRegression();
    demonstratePerceptron();
    demonstratePrincipalComponentAnalysis();
    
    printf("\nAll AI algorithms demonstrated!\n");
    printf("Key algorithms covered:\n");
    printf("- Neural Networks (backpropagation, activation functions)\n");
    printf("- Decision Trees (Gini impurity, recursive splitting)\n");
    printf("- K-Means Clustering (centroid calculation, convergence)\n");
    printf("- Genetic Algorithm (selection, crossover, mutation)\n");
    printf("- Linear Regression (least squares, R-squared)\n");
    printf("- Perceptron (linear classification, weight updates)\n");
    printf("- Principal Component Analysis (eigenvalues, eigenvectors)\n");
    
    return 0;
}
