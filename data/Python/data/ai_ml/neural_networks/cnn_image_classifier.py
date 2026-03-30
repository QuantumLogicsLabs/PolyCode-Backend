"""
CNN Image Classifier
===================

Convolutional Neural Network for image classification using NumPy.
Demonstrates CNN concepts: convolutions, pooling, flattening, fully connected layers.
"""

import numpy as np
import matplotlib.pyplot as plt
from typing import List, Tuple, Optional
import json
import os

class ConvLayer:
    """Convolutional layer implementation"""
    
    def __init__(self, input_channels: int, num_filters: int, kernel_size: int, 
                 stride: int = 1, padding: int = 0):
        self.input_channels = input_channels
        self.num_filters = num_filters
        self.kernel_size = kernel_size
        self.stride = stride
        self.padding = padding
        
        # Initialize random weights
        self.weights = np.random.randn(num_filters, input_channels, kernel_size, kernel_size) * 0.01
        self.biases = np.zeros(num_filters)
        
        # Cache for backward pass
        self.input_cache = None
        self.col_cache = None
    
    def forward(self, x: np.ndarray) -> np.ndarray:
        """Forward pass through convolutional layer"""
        batch_size, channels, height, width = x.shape
        
        # Add padding if needed
        if self.padding > 0:
            x_padded = np.pad(x, ((0, 0), (0, 0), 
                              (self.padding, self.padding), (self.padding, self.padding)), 
                              mode='constant')
        else:
            x_padded = x
        
        # Calculate output dimensions
        out_height = (height + 2 * self.padding - self.kernel_size) // self.stride + 1
        out_width = (width + 2 * self.padding - self.kernel_size) // self.stride + 1
        
        # Initialize output
        output = np.zeros((batch_size, self.num_filters, out_height, out_width))
        
        # Perform convolution
        for b in range(batch_size):
            for f in range(self.num_filters):
                for h in range(out_height):
                    for w in range(out_width):
                        h_start = h * self.stride
                        h_end = h_start + self.kernel_size
                        w_start = w * self.stride
                        w_end = w_start + self.kernel_size
                        
                        # Extract patch
                        patch = x_padded[b, :, h_start:h_end, w_start:w_end]
                        
                        # Convolution operation
                        output[b, f, h, w] = np.sum(patch * self.weights[f]) + self.biases[f]
        
        # Cache for backward pass
        self.input_cache = x_padded
        self.col_cache = output
        
        return output
    
    def relu(self, x: np.ndarray) -> np.ndarray:
        """ReLU activation function"""
        return np.maximum(0, x)

class MaxPoolLayer:
    """Max pooling layer"""
    
    def __init__(self, pool_size: int = 2, stride: int = 2):
        self.pool_size = pool_size
        self.stride = stride
    
    def forward(self, x: np.ndarray) -> np.ndarray:
        """Forward pass through max pooling"""
        batch_size, channels, height, width = x.shape
        
        # Calculate output dimensions
        out_height = height // self.stride
        out_width = width // self.stride
        
        # Initialize output
        output = np.zeros((batch_size, channels, out_height, out_width))
        
        # Perform max pooling
        for b in range(batch_size):
            for c in range(channels):
                for h in range(out_height):
                    for w in range(out_width):
                        h_start = h * self.stride
                        h_end = h_start + self.pool_size
                        w_start = w * self.stride
                        w_end = w_start + self.pool_size
                        
                        # Extract patch and take max
                        patch = x[b, c, h_start:h_end, w_start:w_end]
                        output[b, c, h, w] = np.max(patch)
        
        return output

class CNN:
    """Simple Convolutional Neural Network"""
    
    def __init__(self, input_shape: Tuple[int, int, int], num_classes: int):
        self.input_shape = input_shape
        self.num_classes = num_classes
        
        # Network architecture
        self.conv1 = ConvLayer(input_shape[0], 16, 3, stride=1, padding=1)
        self.pool1 = MaxPoolLayer(pool_size=2, stride=2)
        self.conv2 = ConvLayer(16, 32, 3, stride=1, padding=1)
        self.pool2 = MaxPoolLayer(pool_size=2, stride=2)
        
        # Calculate flattened size after convolutions and pooling
        conv_output_size = ((input_shape[1] + 2) // 2) * ((input_shape[2] + 2) // 2) * 32
        
        # Fully connected layers
        self.fc1_weights = np.random.randn(conv_output_size, 128) * 0.01
        self.fc1_biases = np.zeros(128)
        self.fc2_weights = np.random.randn(128, num_classes) * 0.01
        self.fc2_biases = np.zeros(num_classes)
        
        # Training parameters
        self.learning_rate = 0.001
        self.loss_history = []
    
    def relu(self, x: np.ndarray) -> np.ndarray:
        """ReLU activation function"""
        return np.maximum(0, x)
    
    def softmax(self, x: np.ndarray) -> np.ndarray:
        """Softmax activation function"""
        exp_x = np.exp(x - np.max(x, axis=1, keepdims=True))
        return exp_x / np.sum(exp_x, axis=1, keepdims=True)
    
    def forward(self, x: np.ndarray) -> np.ndarray:
        """Forward pass through the network"""
        # Convolutional layers
        conv1_out = self.conv1.forward(x)
        conv1_out = self.relu(conv1_out)
        pool1_out = self.pool1.forward(conv1_out)
        
        conv2_out = self.conv2.forward(pool1_out)
        conv2_out = self.relu(conv2_out)
        pool2_out = self.pool2.forward(conv2_out)
        
        # Flatten
        flattened = pool2_out.reshape(pool2_out.shape[0], -1)
        
        # Fully connected layers
        fc1_out = np.dot(flattened, self.fc1_weights) + self.fc1_biases
        fc1_out = self.relu(fc1_out)
        
        fc2_out = np.dot(fc1_out, self.fc2_weights) + self.fc2_biases
        output = self.softmax(fc2_out)
        
        return output
    
    def backward(self, x: np.ndarray, y: np.ndarray, output: np.ndarray) -> None:
        """Backward pass and weight updates"""
        batch_size = x.shape[0]
        
        # Simplified backward pass (in practice, this would be much more complex)
        # This is a basic implementation for demonstration
        
        # Update output layer
        output_error = output - y
        fc2_grad = np.dot(self.fc1_weights.T, output_error) / batch_size
        self.fc2_weights -= self.learning_rate * np.dot(self.relu(np.dot(x, self.fc1_weights) + self.fc1_biases).T, fc2_grad)
        self.fc2_biases -= self.learning_rate * np.sum(fc2_grad, axis=0)
        
        # Update first fully connected layer
        fc1_error = np.dot(output_error, self.fc2_weights.T) * (self.relu(np.dot(x, self.fc1_weights) + self.fc1_biases) > 0)
        fc1_grad = np.dot(flattened.T, fc1_error) / batch_size
        # Note: In practice, we'd need to backpropagate through pooling and conv layers
    
    def train(self, X: np.ndarray, y: np.ndarray, epochs: int = 100) -> List[float]:
        """Train the CNN"""
        print(f"Training CNN for {epochs} epochs...")
        
        for epoch in range(epochs):
            total_loss = 0
            
            # Mini-batch training
            batch_size = min(32, len(X))
            for i in range(0, len(X), batch_size):
                batch_X = X[i:i+batch_size]
                batch_y = y[i:i+batch_size]
                
                # Forward pass
                output = self.forward(batch_X)
                
                # Calculate loss (categorical cross-entropy)
                loss = -np.mean(np.sum(batch_y * np.log(output + 1e-8), axis=1))
                total_loss += loss
                
                # Backward pass
                self.backward(batch_X, batch_y, output)
            
            avg_loss = total_loss / (len(X) // batch_size)
            self.loss_history.append(avg_loss)
            
            if epoch % 10 == 0:
                print(f"Epoch {epoch}, Loss: {avg_loss:.6f}")
        
        return self.loss_history
    
    def predict(self, x: np.ndarray) -> np.ndarray:
        """Make predictions"""
        return self.forward(x)
    
    def save_model(self, filename: str) -> None:
        """Save model weights to file"""
        model_data = {
            'conv1_weights': self.conv1.weights.tolist(),
            'conv2_weights': self.conv2.weights.tolist(),
            'fc1_weights': self.fc1_weights.tolist(),
            'fc2_weights': self.fc2_weights.tolist(),
            'input_shape': self.input_shape,
            'num_classes': self.num_classes
        }
        
        with open(filename, 'w') as f:
            json.dump(model_data, f, indent=2)
        
        print(f"Model saved to {filename}")

def load_mnist_data(num_samples: int = 1000) -> Tuple[np.ndarray, np.ndarray]:
    """Load or generate sample MNIST-like data"""
    # For demonstration, generate random image-like data
    X = np.random.rand(num_samples, 1, 28, 28)  # Grayscale images
    y = np.random.randint(0, 10, (num_samples, 1))  # 10 classes
    
    # One-hot encode labels
    y_one_hot = np.zeros((num_samples, 10))
    y_one_hot[np.arange(num_samples), y] = 1
    
    return X, y_one_hot

def visualize_training(loss_history: List[float]) -> None:
    """Visualize training loss"""
    plt.figure(figsize=(10, 6))
    plt.plot(loss_history)
    plt.title('CNN Training Loss')
    plt.xlabel('Epoch')
    plt.ylabel('Loss')
    plt.grid(True)
    plt.show()

def visualize_predictions(X: np.ndarray, predictions: np.ndarray, true_labels: np.ndarray, 
                       num_samples: int = 10) -> None:
    """Visualize sample predictions"""
    plt.figure(figsize=(15, 8))
    
    for i in range(min(num_samples, len(X))):
        plt.subplot(2, 5, i + 1)
        plt.imshow(X[i, 0], cmap='gray')
        plt.title(f'True: {np.argmax(true_labels[i])}, Pred: {np.argmax(predictions[i])}')
        plt.axis('off')
    
    plt.tight_layout()
    plt.show()

def main():
    """Main function to demonstrate the CNN"""
    print("=== CNN Image Classifier Demo ===\n")
    
    # Load sample data
    X_train, y_train = load_mnist_data(1000)
    X_test, y_test = load_mnist_data(200)
    
    print(f"Training data shape: {X_train.shape}")
    print(f"Test data shape: {X_test.shape}")
    
    # Create and train CNN
    cnn = CNN(input_shape=(1, 28, 28), num_classes=10)
    
    # Train the network
    loss_history = cnn.train(X_train, y_train, epochs=50)
    
    # Test the network
    predictions = cnn.predict(X_test)
    accuracy = np.mean(np.argmax(predictions, axis=1) == np.argmax(y_test, axis=1)) * 100
    
    print(f"\nTraining completed!")
    print(f"Test accuracy: {accuracy:.2f}%")
    
    # Save model
    cnn.save_model('cnn_model.json')
    
    # Visualize results
    visualize_training(loss_history)
    visualize_predictions(X_test, predictions, y_test)
    
    # Show sample predictions
    print("\nSample predictions:")
    for i in range(5):
        pred_class = np.argmax(predictions[i])
        true_class = np.argmax(y_test[i])
        confidence = np.max(predictions[i])
        status = "✓" if pred_class == true_class else "✗"
        print(f"Sample {i+1}: True={true_class}, Predicted={pred_class}, Confidence={confidence:.3f}, {status}")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Training: python cnn_image_classifier.py
2. The CNN will train on image-like data
3. Visualizes training loss and sample predictions
4. Saves model weights to cnn_model.json

Key Concepts:
- Convolutional Layers: Feature extraction from images
- Pooling Layers: Spatial downsampling
- Activation Functions: ReLU for non-linearity
- Softmax: Multi-class probability distribution
- Backpropagation: Gradient computation and weight updates

Applications:
- Image classification
- Object detection
- Medical image analysis
- Autonomous driving perception
- Facial recognition

Architecture:
Input -> Conv -> ReLU -> Pool -> Conv -> ReLU -> Pool -> FC -> Softmax -> Output
"""
