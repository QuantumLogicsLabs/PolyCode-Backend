"""
Simple Neural Network Implementation
================================

A basic feedforward neural network implemented from scratch using NumPy.
Demonstrates fundamental concepts: forward propagation, backpropagation, gradient descent.
"""

import numpy as np
import matplotlib.pyplot as plt
from typing import List, Tuple
import json

class SimpleNeuralNetwork:
    """Simple feedforward neural network with one hidden layer"""
    
    def __init__(self, input_size: int, hidden_size: int, output_size: int, 
                 learning_rate: float = 0.1):
        self.input_size = input_size
        self.hidden_size = hidden_size
        self.output_size = output_size
        self.learning_rate = learning_rate
        
        # Initialize weights with random values
        self.weights1 = np.random.randn(input_size, hidden_size)
        self.weights2 = np.random.randn(hidden_size, output_size)
        self.bias1 = np.zeros((1, hidden_size))
        self.bias2 = np.zeros((1, output_size))
        
        # Store training history
        self.loss_history = []
        
    def sigmoid(self, x: np.ndarray) -> np.ndarray:
        """Sigmoid activation function"""
        return 1 / (1 + np.exp(-x))
    
    def sigmoid_derivative(self, x: np.ndarray) -> np.ndarray:
        """Derivative of sigmoid function"""
        return x * (1 - x)
    
    def forward(self, X: np.ndarray) -> Tuple[np.ndarray, np.ndarray, np.ndarray]:
        """Forward propagation through the network"""
        # Hidden layer
        hidden_input = np.dot(X, self.weights1) + self.bias1
        hidden_output = self.sigmoid(hidden_input)
        
        # Output layer
        final_input = np.dot(hidden_output, self.weights2) + self.bias2
        final_output = self.sigmoid(final_input)
        
        return hidden_output, final_output, final_input
    
    def backward(self, X: np.ndarray, y: np.ndarray, 
                hidden_output: np.ndarray, final_output: np.ndarray) -> None:
        """Backward propagation and weight updates"""
        m = X.shape[0]  # number of samples
        
        # Calculate output layer error
        output_error = y - final_output
        output_delta = output_error * self.sigmoid_derivative(final_output)
        
        # Calculate hidden layer error
        hidden_error = output_delta.dot(self.weights2.T)
        hidden_delta = hidden_error * self.sigmoid_derivative(hidden_output)
        
        # Update weights and biases
        self.weights2 += hidden_output.T.dot(output_delta) * self.learning_rate
        self.bias2 += np.sum(output_delta, axis=0, keepdims=True) * self.learning_rate
        self.weights1 += X.T.dot(hidden_delta) * self.learning_rate
        self.bias1 += np.sum(hidden_delta, axis=0, keepdims=True) * self.learning_rate
    
    def train(self, X: np.ndarray, y: np.ndarray, epochs: int = 1000) -> List[float]:
        """Train the neural network"""
        print(f"Training neural network for {epochs} epochs...")
        
        for epoch in range(epochs):
            # Forward propagation
            hidden_output, final_output, _ = self.forward(X)
            
            # Calculate loss (mean squared error)
            loss = np.mean((y - final_output) ** 2)
            self.loss_history.append(loss)
            
            # Backward propagation
            self.backward(X, y, hidden_output, final_output)
            
            if epoch % 100 == 0:
                print(f"Epoch {epoch}, Loss: {loss:.6f}")
        
        return self.loss_history
    
    def predict(self, X: np.ndarray) -> np.ndarray:
        """Make predictions"""
        _, final_output, _ = self.forward(X)
        return final_output
    
    def save_weights(self, filename: str) -> None:
        """Save network weights to file"""
        weights_data = {
            'weights1': self.weights1.tolist(),
            'weights2': self.weights2.tolist(),
            'bias1': self.bias1.tolist(),
            'bias2': self.bias2.tolist(),
            'architecture': {
                'input_size': self.input_size,
                'hidden_size': self.hidden_size,
                'output_size': self.output_size
            }
        }
        
        with open(filename, 'w') as f:
            json.dump(weights_data, f, indent=2)
        
        print(f"Weights saved to {filename}")
    
    def load_weights(self, filename: str) -> None:
        """Load network weights from file"""
        try:
            with open(filename, 'r') as f:
                weights_data = json.load(f)
            
            self.weights1 = np.array(weights_data['weights1'])
            self.weights2 = np.array(weights_data['weights2'])
            self.bias1 = np.array(weights_data['bias1'])
            self.bias2 = np.array(weights_data['bias2'])
            
            print(f"Weights loaded from {filename}")
        except FileNotFoundError:
            print(f"File {filename} not found. Using random weights.")

def generate_sample_data(n_samples: int = 100) -> Tuple[np.ndarray, np.ndarray]:
    """Generate sample data for training"""
    # XOR problem
    X = np.array([[0, 0], [0, 1], [1, 0], [1, 1]])
    y = np.array([[0], [1], [1], [0]])
    
    # Repeat the data
    X = np.tile(X, (n_samples // 4, 1))[:n_samples]
    y = np.tile(y, (n_samples // 4, 1))[:n_samples]
    
    return X, y

def visualize_training(loss_history: List[float]) -> None:
    """Visualize training loss"""
    plt.figure(figsize=(10, 6))
    plt.plot(loss_history)
    plt.title('Training Loss Over Time')
    plt.xlabel('Epoch')
    plt.ylabel('Loss')
    plt.grid(True)
    plt.show()

def main():
    """Main function to demonstrate the neural network"""
    print("=== Simple Neural Network Demo ===\n")
    
    # Generate sample data
    X_train, y_train = generate_sample_data(200)
    
    print(f"Training data shape: {X_train.shape}")
    print(f"Target data shape: {y_train.shape}")
    
    # Create and train neural network
    nn = SimpleNeuralNetwork(input_size=2, hidden_size=4, output_size=1, learning_rate=0.1)
    
    # Train the network
    loss_history = nn.train(X_train, y_train, epochs=1000)
    
    # Test the network
    predictions = nn.predict(X_train)
    accuracy = np.mean((predictions > 0.5) == (y_train > 0.5)) * 100
    
    print(f"\nTraining completed!")
    print(f"Final accuracy: {accuracy:.2f}%")
    
    # Save weights
    nn.save_weights('nn_weights.json')
    
    # Visualize training progress
    visualize_training(loss_history)
    
    # Test with new data
    print("\nTesting with new data:")
    test_X, test_y = generate_sample_data(20)
    test_predictions = nn.predict(test_X)
    
    for i, (x, y) in enumerate(zip(test_X, test_y)):
        pred = test_predictions[i][0]
        actual = y[0]
        status = "✓" if (pred > 0.5) == (actual > 0.5) else "✗"
        print(f"Input: {x}, Target: {actual}, Predicted: {pred:.3f}, {status}")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Training: python simple_nn.py
2. The network will train on XOR problem data
3. Visualizes training loss
4. Saves weights to nn_weights.json
5. Tests predictions on new data

Key Concepts:
- Forward Propagation: Computing outputs through the network
- Backward Propagation: Computing gradients and updating weights
- Activation Functions: Sigmoid for non-linearity
- Loss Function: Mean squared error for regression
- Gradient Descent: Weight optimization algorithm

Applications:
- Pattern recognition
- Function approximation
- Classification tasks
- Time series prediction
"""
