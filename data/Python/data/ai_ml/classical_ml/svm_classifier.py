"""
Support Vector Machine (SVM) Implementation
============================================

SVM classifier from scratch using gradient descent.
Demonstrates maximum margin classification, kernel methods, and optimization.
"""

import numpy as np
import matplotlib.pyplot as plt
from typing import List, Tuple, Optional, Callable
import json

class SVM:
    """Support Vector Machine implementation"""
    
    def __init__(self, C: float = 1.0, learning_rate: float = 0.001, 
                 n_iterations: int = 1000, kernel: str = 'linear', 
                 gamma: float = 1.0, degree: int = 3):
        self.C = C  # Regularization parameter
        self.learning_rate = learning_rate
        self.n_iterations = n_iterations
        self.kernel_name = kernel
        self.gamma = gamma
        self.degree = degree
        
        self.weights = None
        self.bias = None
        self.support_vectors = None
        self.loss_history = []
        
        # Kernel function
        self.kernel = self._get_kernel_function(kernel)
    
    def _get_kernel_function(self, kernel_name: str) -> Callable:
        """Get kernel function based on name"""
        if kernel_name == 'linear':
            return lambda x1, x2: np.dot(x1, x2)
        elif kernel_name == 'rbf':
            return lambda x1, x2: np.exp(-self.gamma * np.linalg.norm(x1 - x2) ** 2)
        elif kernel_name == 'poly':
            return lambda x1, x2: (self.gamma * np.dot(x1, x2) + 1) ** self.degree
        elif kernel_name == 'sigmoid':
            return lambda x1, x2: np.tanh(self.gamma * np.dot(x1, x2))
        else:
            raise ValueError(f"Unknown kernel: {kernel_name}")
    
    def _compute_kernel_matrix(self, X: np.ndarray) -> np.ndarray:
        """Compute kernel matrix for all pairs of samples"""
        n_samples = X.shape[0]
        K = np.zeros((n_samples, n_samples))
        
        for i in range(n_samples):
            for j in range(n_samples):
                K[i, j] = self.kernel(X[i], X[j])
        
        return K
    
    def hinge_loss(self, y: np.ndarray, margin: np.ndarray) -> np.ndarray:
        """Calculate hinge loss"""
        return np.maximum(0, 1 - y * margin)
    
    def fit(self, X: np.ndarray, y: np.ndarray) -> None:
        """Train the SVM classifier"""
        print(f"Training SVM with {self.kernel_name} kernel...")
        
        n_samples, n_features = X.shape
        
        # Convert labels to -1 and 1
        y = np.where(y == 0, -1, y)
        
        if self.kernel_name == 'linear':
            # Linear SVM (primal form)
            self.weights = np.zeros(n_features)
            self.bias = 0
            
            for iteration in range(self.n_iterations):
                # Compute margins
                margins = y * (np.dot(X, self.weights) + self.bias)
                
                # Compute hinge loss
                losses = self.hinge_loss(y, margins)
                total_loss = np.sum(losses) + 0.5 * np.sum(self.weights ** 2)
                self.loss_history.append(total_loss)
                
                # Compute gradients
                for i in range(n_samples):
                    if margins[i] < 1:  # Misclassified or within margin
                        # Update weights and bias
                        self.weights += self.learning_rate * (
                            y[i] * X[i] - self.C * self.weights
                        )
                        self.bias += self.learning_rate * y[i] * self.C
                    else:
                        # Only regularization term
                        self.weights -= self.learning_rate * self.C * self.weights
                
                if iteration % 100 == 0:
                    print(f"Iteration {iteration}, Loss: {total_loss:.6f}")
        
        else:
            # Kernel SVM (dual form)
            # Initialize Lagrange multipliers
            alpha = np.zeros(n_samples)
            
            # Compute kernel matrix
            K = self._compute_kernel_matrix(X)
            
            for iteration in range(self.n_iterations):
                # Compute decision function
                decision = np.sum(alpha * y[:, np.newaxis] * K, axis=0)
                
                # Compute loss
                losses = self.hinge_loss(y, decision)
                total_loss = np.sum(losses) + 0.5 * np.sum(alpha * y[:, np.newaxis] * K * alpha[:, np.newaxis] * y[:, np.newaxis])
                self.loss_history.append(total_loss)
                
                # Update alpha (simplified gradient descent)
                for i in range(n_samples):
                    if decision[i] < 1:  # Misclassified or within margin
                        alpha[i] += self.learning_rate
                    else:
                        alpha[i] -= self.learning_rate * 0.01  # Small regularization
                    
                    # Clip alpha to [0, C]
                    alpha[i] = np.clip(alpha[i], 0, self.C)
                
                if iteration % 100 == 0:
                    print(f"Iteration {iteration}, Loss: {total_loss:.6f}")
            
            # Store support vectors
            support_vector_indices = alpha > 1e-5
            self.support_vectors = X[support_vector_indices]
            self.support_vector_alphas = alpha[support_vector_indices]
            self.support_vector_labels = y[support_vector_indices]
        
        print("Training completed!")
    
    def decision_function(self, X: np.ndarray) -> np.ndarray:
        """Compute decision function values"""
        if self.kernel_name == 'linear':
            return np.dot(X, self.weights) + self.bias
        else:
            # Kernel SVM decision function
            decision = np.zeros(X.shape[0])
            
            for i in range(X.shape[0]):
                for j in range(len(self.support_vectors)):
                    decision[i] += (self.support_vector_alphas[j] * 
                                  self.support_vector_labels[j] * 
                                  self.kernel(X[i], self.support_vectors[j]))
            
            return decision
    
    def predict(self, X: np.ndarray) -> np.ndarray:
        """Make predictions"""
        decision_values = self.decision_function(X)
        predictions = np.sign(decision_values)
        
        # Convert back to 0 and 1
        return np.where(predictions == -1, 0, 1)
    
    def predict_proba(self, X: np.ndarray) -> np.ndarray:
        """Get probability-like scores (not true probabilities)"""
        decision_values = self.decision_function(X)
        # Convert to probabilities using sigmoid-like function
        probabilities = 1 / (1 + np.exp(-decision_values))
        
        # Return probabilities for both classes
        return np.column_stack([1 - probabilities, probabilities])
    
    def get_support_vectors(self) -> Optional[np.ndarray]:
        """Get support vectors"""
        if self.kernel_name == 'linear':
            # For linear SVM, find support vectors
            margins = self.decision_function(self.X_train) if hasattr(self, 'X_train') else None
            if margins is not None:
                support_vector_mask = np.abs(margins) <= 1 + 1e-5
                return self.X_train[support_vector_mask] if hasattr(self, 'X_train') else None
        else:
            return self.support_vectors
    
    def get_margin(self, X: np.ndarray, y: np.ndarray) -> float:
        """Calculate margin width"""
        if self.kernel_name == 'linear':
            # Margin = 2 / ||w||
            return 2 / np.linalg.norm(self.weights)
        else:
            # For kernel SVM, approximate margin using support vectors
            decision_values = self.decision_function(self.support_vectors)
            return np.min(np.abs(decision_values))
    
    def save_model(self, filename: str) -> None:
        """Save model to file"""
        model_data = {
            'kernel_name': self.kernel_name,
            'C': self.C,
            'learning_rate': self.learning_rate,
            'n_iterations': self.n_iterations,
            'gamma': self.gamma,
            'degree': self.degree
        }
        
        if self.kernel_name == 'linear':
            model_data['weights'] = self.weights.tolist()
            model_data['bias'] = self.bias
        else:
            model_data['support_vectors'] = self.support_vectors.tolist() if self.support_vectors is not None else None
            model_data['support_vector_alphas'] = self.support_vector_alphas.tolist()
            model_data['support_vector_labels'] = self.support_vector_labels.tolist()
        
        with open(filename, 'w') as f:
            json.dump(model_data, f, indent=2)
        
        print(f"Model saved to {filename}")

def generate_linear_data(n_samples: int = 200, margin: float = 2.0) -> Tuple[np.ndarray, np.ndarray]:
    """Generate linearly separable data"""
    np.random.seed(42)
    
    # Generate class 1
    n_samples_per_class = n_samples // 2
    
    class1 = np.random.randn(n_samples_per_class, 2)
    class1[:, 0] += margin  # Shift to the right
    
    # Generate class 2
    class2 = np.random.randn(n_samples_per_class, 2)
    class2[:, 0] -= margin  # Shift to the left
    
    # Combine data
    X = np.vstack([class1, class2])
    y = np.hstack([np.ones(n_samples_per_class), np.zeros(n_samples_per_class)])
    
    return X, y

def generate_nonlinear_data(n_samples: int = 200) -> Tuple[np.ndarray, np.ndarray]:
    """Generate non-linearly separable data (circular pattern)"""
    np.random.seed(42)
    
    n_samples_per_class = n_samples // 2
    
    # Class 1: Inner circle
    r1 = np.random.uniform(0, 2, n_samples_per_class)
    theta1 = np.random.uniform(0, 2 * np.pi, n_samples_per_class)
    class1 = np.column_stack([r1 * np.cos(theta1), r1 * np.sin(theta1)])
    
    # Class 2: Outer circle
    r2 = np.random.uniform(3, 5, n_samples_per_class)
    theta2 = np.random.uniform(0, 2 * np.pi, n_samples_per_class)
    class2 = np.column_stack([r2 * np.cos(theta2), r2 * np.sin(theta2)])
    
    # Combine data
    X = np.vstack([class1, class2])
    y = np.hstack([np.ones(n_samples_per_class), np.zeros(n_samples_per_class)])
    
    return X, y

def generate_xor_data(n_samples: int = 200) -> Tuple[np.ndarray, np.ndarray]:
    """Generate XOR pattern data"""
    np.random.seed(42)
    
    n_samples_per_class = n_samples // 4
    
    # Generate four quadrants
    quadrant1 = np.random.randn(n_samples_per_class, 2) + [2, 2]
    quadrant2 = np.random.randn(n_samples_per_class, 2) + [-2, -2]
    quadrant3 = np.random.randn(n_samples_per_class, 2) + [-2, 2]
    quadrant4 = np.random.randn(n_samples_per_class, 2) + [2, -2]
    
    # Combine data (XOR pattern: same sign -> class 1, different sign -> class 0)
    X = np.vstack([quadrant1, quadrant2, quadrant3, quadrant4])
    y = np.hstack([np.ones(n_samples_per_class * 2), np.zeros(n_samples_per_class * 2)])
    
    return X, y

def visualize_svm_results(X: np.ndarray, y: np.ndarray, svm: SVM, title: str) -> None:
    """Visualize SVM classification results"""
    plt.figure(figsize=(15, 5))
    
    # Plot data points
    plt.subplot(1, 3, 1)
    plt.scatter(X[y == 0][:, 0], X[y == 0][:, 1], c='red', label='Class 0', alpha=0.7)
    plt.scatter(X[y == 1][:, 0], X[y == 1][:, 1], c='blue', label='Class 1', alpha=0.7)
    
    # Plot support vectors
    if svm.get_support_vectors() is not None:
        support_vectors = svm.get_support_vectors()
        plt.scatter(support_vectors[:, 0], support_vectors[:, 1], 
                   s=100, facecolors='none', edgecolors='green', linewidth=2, 
                   label='Support Vectors')
    
    plt.title(f'{title} - Data Points')
    plt.xlabel('Feature 1')
    plt.ylabel('Feature 2')
    plt.legend()
    plt.grid(True)
    
    # Plot decision boundary
    plt.subplot(1, 3, 2)
    
    # Create mesh grid
    x_min, x_max = X[:, 0].min() - 1, X[:, 0].max() + 1
    y_min, y_max = X[:, 1].min() - 1, X[:, 1].max() + 1
    xx, yy = np.meshgrid(np.arange(x_min, x_max, 0.1), np.arange(y_min, y_max, 0.1))
    
    # Predict on mesh grid
    mesh_points = np.c_[xx.ravel(), yy.ravel()]
    Z = svm.predict(mesh_points)
    Z = Z.reshape(xx.shape)
    
    # Plot decision boundary
    plt.contourf(xx, yy, Z, alpha=0.3, cmap='RdBu')
    plt.scatter(X[y == 0][:, 0], X[y == 0][:, 1], c='red', alpha=0.7)
    plt.scatter(X[y == 1][:, 0], X[y == 1][:, 1], c='blue', alpha=0.7)
    
    plt.title(f'{title} - Decision Boundary')
    plt.xlabel('Feature 1')
    plt.ylabel('Feature 2')
    plt.grid(True)
    
    # Plot loss history
    plt.subplot(1, 3, 3)
    plt.plot(svm.loss_history)
    plt.xlabel('Iteration')
    plt.ylabel('Loss')
    plt.title('Training Loss')
    plt.grid(True)
    
    plt.tight_layout()
    plt.show()

def compare_kernels(X: np.ndarray, y: np.ndarray) -> None:
    """Compare different kernel functions"""
    kernels = ['linear', 'rbf', 'poly', 'sigmoid']
    
    print("=== Comparing Different Kernels ===")
    
    for kernel in kernels:
        print(f"\nTraining SVM with {kernel} kernel...")
        
        if kernel == 'linear':
            svm = SVM(C=1.0, learning_rate=0.001, n_iterations=1000, kernel=kernel)
        else:
            svm = SVM(C=1.0, learning_rate=0.001, n_iterations=1000, kernel=kernel, 
                      gamma=0.1, degree=3)
        
        svm.fit(X, y)
        
        # Calculate accuracy
        predictions = svm.predict(X)
        accuracy = np.mean(predictions == y)
        
        print(f"Accuracy: {accuracy:.4f}")
        
        # Visualize results
        visualize_svm_results(X, y, svm, f'SVM ({kernel} kernel)')

def main():
    """Main function to demonstrate SVM"""
    print("=== Support Vector Machine Demo ===\n")
    
    # Test on linear data
    print("1. Linear SVM on Linear Data:")
    X_linear, y_linear = generate_linear_data(200)
    
    svm_linear = SVM(C=1.0, learning_rate=0.001, n_iterations=1000, kernel='linear')
    svm_linear.fit(X_linear, y_linear)
    
    predictions_linear = svm_linear.predict(X_linear)
    accuracy_linear = np.mean(predictions_linear == y_linear)
    
    print(f"Linear SVM Accuracy: {accuracy_linear:.4f}")
    print(f"Margin: {svm_linear.get_margin(X_linear, y_linear):.4f}")
    
    visualize_svm_results(X_linear, y_linear, svm_linear, "Linear SVM")
    
    # Test on non-linear data with different kernels
    print("\n2. SVM on Non-linear Data:")
    X_nonlinear, y_nonlinear = generate_nonlinear_data(200)
    
    # Linear kernel (should perform poorly)
    svm_linear_nl = SVM(C=1.0, learning_rate=0.001, n_iterations=1000, kernel='linear')
    svm_linear_nl.fit(X_nonlinear, y_nonlinear)
    
    predictions_linear_nl = svm_linear_nl.predict(X_nonlinear)
    accuracy_linear_nl = np.mean(predictions_linear_nl == y_nonlinear)
    
    print(f"Linear Kernel Accuracy: {accuracy_linear_nl:.4f}")
    visualize_svm_results(X_nonlinear, y_nonlinear, svm_linear_nl, "Linear SVM (Non-linear Data)")
    
    # RBF kernel (should perform well)
    svm_rbf = SVM(C=1.0, learning_rate=0.001, n_iterations=1000, kernel='rbf', gamma=0.1)
    svm_rbf.fit(X_nonlinear, y_nonlinear)
    
    predictions_rbf = svm_rbf.predict(X_nonlinear)
    accuracy_rbf = np.mean(predictions_rbf == y_nonlinear)
    
    print(f"RBF Kernel Accuracy: {accuracy_rbf:.4f}")
    visualize_svm_results(X_nonlinear, y_nonlinear, svm_rbf, "RBF SVM")
    
    # Test on XOR data
    print("\n3. SVM on XOR Data:")
    X_xor, y_xor = generate_xor_data(200)
    
    # Compare kernels on XOR data
    compare_kernels(X_xor, y_xor)
    
    # Save best model
    svm_rbf.save_model('svm_model.json')
    
    # Show sample predictions
    print("\nSample Predictions (RBF SVM on Non-linear Data):")
    for i in range(5):
        point = X_nonlinear[i]
        prediction = svm_rbf.predict(point.reshape(1, -1))[0]
        actual = y_nonlinear[i]
        decision_value = svm_rbf.decision_function(point.reshape(1, -1))[0]
        status = "✓" if prediction == actual else "✗"
        print(f"Point {i+1}: {point}, True={actual}, Predicted={prediction}, "
              f"Decision={decision_value:.3f}, {status}")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Training: python svm_classifier.py
2. Demonstrates SVM on linear and non-linear data
3. Compares different kernel functions
4. Visualizes decision boundaries and support vectors
5. Saves model to svm_model.json

Key Concepts:
- Maximum Margin Classification: Find hyperplane with largest margin
- Support Vectors: Data points that define the margin
- Kernel Trick: Transform data to higher dimensions
- Hinge Loss: Loss function for SVM
- Regularization: Trade-off between margin and misclassification

Applications:
- Text classification
- Image classification
- Bioinformatics
- Handwriting recognition
- Face detection
- Medical diagnosis

Kernel Functions:
- Linear: dot(x, y)
- RBF: exp(-γ||x-y||²)
- Polynomial: (γ·dot(x,y) + 1)^d
- Sigmoid: tanh(γ·dot(x,y))

Advantages:
- Effective in high dimensions
- Memory efficient (uses support vectors)
- Versatile with different kernels
- Robust to overfitting with proper C

Limitations:
- Sensitive to feature scaling
- Requires careful parameter tuning
- Computationally intensive for large datasets
- Interpretation of results can be difficult
"""
