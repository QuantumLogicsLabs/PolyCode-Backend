"""
Linear Regression Implementation
================================

Linear regression from scratch using gradient descent.
Demonstrates supervised learning, optimization, and model evaluation.
"""

import numpy as np
import matplotlib.pyplot as plt
from typing import List, Tuple, Optional
import json

class LinearRegression:
    """Linear regression implementation using gradient descent"""
    
    def __init__(self, learning_rate: float = 0.01, n_iterations: int = 1000, 
                 regularization: str = None, lambda_param: float = 0.1):
        self.learning_rate = learning_rate
        self.n_iterations = n_iterations
        self.regularization = regularization
        self.lambda_param = lambda_param
        
        self.weights = None
        self.bias = None
        self.loss_history = []
        self.cost_history = []
    
    def initialize_parameters(self, n_features: int) -> None:
        """Initialize model parameters"""
        self.weights = np.zeros(n_features)
        self.bias = 0
    
    def compute_cost(self, X: np.ndarray, y: np.ndarray) -> float:
        """Compute cost function with optional regularization"""
        n_samples = len(y)
        
        # Predictions
        y_pred = np.dot(X, self.weights) + self.bias
        
        # Mean squared error
        mse = np.sum((y_pred - y) ** 2) / (2 * n_samples)
        
        # Add regularization
        if self.regularization == 'l2':
            mse += (self.lambda_param / (2 * n_samples)) * np.sum(self.weights ** 2)
        elif self.regularization == 'l1':
            mse += (self.lambda_param / n_samples) * np.sum(np.abs(self.weights))
        
        return mse
    
    def compute_gradients(self, X: np.ndarray, y: np.ndarray) -> Tuple[np.ndarray, float]:
        """Compute gradients for weights and bias"""
        n_samples, n_features = X.shape
        
        # Predictions
        y_pred = np.dot(X, self.weights) + self.bias
        
        # Compute gradients
        dw = (1 / n_samples) * np.dot(X.T, (y_pred - y))
        db = (1 / n_samples) * np.sum(y_pred - y)
        
        # Add regularization to gradients
        if self.regularization == 'l2':
            dw += (self.lambda_param / n_samples) * self.weights
        elif self.regularization == 'l1':
            dw += (self.lambda_param / n_samples) * np.sign(self.weights)
        
        return dw, db
    
    def fit(self, X: np.ndarray, y: np.ndarray) -> None:
        """Train the linear regression model"""
        print(f"Training Linear Regression for {self.n_iterations} iterations...")
        
        n_samples, n_features = X.shape
        
        # Initialize parameters
        self.initialize_parameters(n_features)
        
        # Gradient descent
        for i in range(self.n_iterations):
            # Compute gradients
            dw, db = self.compute_gradients(X, y)
            
            # Update parameters
            self.weights -= self.learning_rate * dw
            self.bias -= self.learning_rate * db
            
            # Compute and store cost
            cost = self.compute_cost(X, y)
            self.cost_history.append(cost)
            
            if i % 100 == 0:
                print(f"Iteration {i}, Cost: {cost:.6f}")
        
        print("Training completed!")
    
    def predict(self, X: np.ndarray) -> np.ndarray:
        """Make predictions"""
        return np.dot(X, self.weights) + self.bias
    
    def score(self, X: np.ndarray, y: np.ndarray) -> float:
        """Calculate R-squared score"""
        y_pred = self.predict(X)
        
        # Total sum of squares
        ss_tot = np.sum((y - np.mean(y)) ** 2)
        
        # Residual sum of squares
        ss_res = np.sum((y - y_pred) ** 2)
        
        # R-squared
        r2 = 1 - (ss_res / ss_tot)
        
        return r2
    
    def mean_squared_error(self, X: np.ndarray, y: np.ndarray) -> float:
        """Calculate mean squared error"""
        y_pred = self.predict(X)
        return np.mean((y - y_pred) ** 2)
    
    def mean_absolute_error(self, X: np.ndarray, y: np.ndarray) -> float:
        """Calculate mean absolute error"""
        y_pred = self.predict(X)
        return np.mean(np.abs(y - y_pred))
    
    def save_model(self, filename: str) -> None:
        """Save model to file"""
        model_data = {
            'weights': self.weights.tolist(),
            'bias': self.bias,
            'learning_rate': self.learning_rate,
            'n_iterations': self.n_iterations,
            'regularization': self.regularization,
            'lambda_param': self.lambda_param
        }
        
        with open(filename, 'w') as f:
            json.dump(model_data, f, indent=2)
        
        print(f"Model saved to {filename}")
    
    def load_model(self, filename: str) -> None:
        """Load model from file"""
        try:
            with open(filename, 'r') as f:
                model_data = json.load(f)
            
            self.weights = np.array(model_data['weights'])
            self.bias = model_data['bias']
            self.learning_rate = model_data['learning_rate']
            self.n_iterations = model_data['n_iterations']
            self.regularization = model_data['regularization']
            self.lambda_param = model_data['lambda_param']
            
            print(f"Model loaded from {filename}")
        except FileNotFoundError:
            print(f"File {filename} not found. Using random weights.")

def generate_sample_data(n_samples: int = 100, noise: float = 0.1) -> Tuple[np.ndarray, np.ndarray]:
    """Generate sample linear regression data"""
    np.random.seed(42)
    
    # Generate features
    X = np.random.rand(n_samples, 1) * 10
    
    # Generate target with linear relationship + noise
    y = 2 * X.squeeze() + 1 + np.random.normal(0, noise, n_samples)
    
    return X, y

def generate_multivariate_data(n_samples: int = 100, n_features: int = 3) -> Tuple[np.ndarray, np.ndarray]:
    """Generate multivariate regression data"""
    np.random.seed(42)
    
    # Generate features
    X = np.random.rand(n_samples, n_features) * 10
    
    # True weights
    true_weights = np.array([2.5, -1.8, 3.2])
    true_bias = 1.5
    
    # Generate target with linear relationship + noise
    y = np.dot(X, true_weights) + true_bias + np.random.normal(0, 0.5, n_samples)
    
    return X, y

def visualize_results(X: np.ndarray, y: np.ndarray, y_pred: np.ndarray, 
                      cost_history: List[float], title: str = "Linear Regression Results") -> None:
    """Visualize regression results and training progress"""
    fig, axes = plt.subplots(1, 2, figsize=(15, 5))
    
    # Plot regression line
    ax = axes[0]
    ax.scatter(X, y, alpha=0.6, label='Actual Data')
    ax.plot(X, y_pred, 'r-', label='Predicted Line', linewidth=2)
    ax.set_xlabel('X')
    ax.set_ylabel('y')
    ax.set_title(f'{title} - Regression Line')
    ax.legend()
    ax.grid(True)
    
    # Plot cost history
    ax = axes[1]
    ax.plot(cost_history)
    ax.set_xlabel('Iteration')
    ax.set_ylabel('Cost')
    ax.set_title('Training Cost Over Time')
    ax.grid(True)
    
    plt.tight_layout()
    plt.show()

def visualize_multivariate_results(X: np.ndarray, y: np.ndarray, y_pred: np.ndarray, 
                                  cost_history: List[float]) -> None:
    """Visualize multivariate regression results"""
    fig, axes = plt.subplots(2, 2, figsize=(15, 10))
    
    # Plot actual vs predicted
    ax = axes[0, 0]
    ax.scatter(y, y_pred, alpha=0.6)
    ax.plot([y.min(), y.max()], [y.min(), y.max()], 'r--', label='Perfect Prediction')
    ax.set_xlabel('Actual y')
    ax.set_ylabel('Predicted y')
    ax.set_title('Actual vs Predicted')
    ax.legend()
    ax.grid(True)
    
    # Plot residuals
    ax = axes[0, 1]
    residuals = y - y_pred
    ax.scatter(y_pred, residuals, alpha=0.6)
    ax.axhline(y=0, color='r', linestyle='--')
    ax.set_xlabel('Predicted y')
    ax.set_ylabel('Residuals')
    ax.set_title('Residual Plot')
    ax.grid(True)
    
    # Plot cost history
    ax = axes[1, 0]
    ax.plot(cost_history)
    ax.set_xlabel('Iteration')
    ax.set_ylabel('Cost')
    ax.set_title('Training Cost Over Time')
    ax.grid(True)
    
    # Plot feature importance (absolute weights)
    ax = axes[1, 1]
    feature_names = [f'Feature {i+1}' for i in range(X.shape[1])]
    weights = np.abs([2.5, -1.8, 3.2])  # True weights for comparison
    ax.bar(feature_names, weights)
    ax.set_ylabel('Absolute Weight')
    ax.set_title('Feature Importance (True Weights)')
    ax.grid(True)
    
    plt.tight_layout()
    plt.show()

def compare_models(X_train: np.ndarray, y_train: np.ndarray, X_test: np.ndarray, y_test: np.ndarray) -> None:
    """Compare different regularization approaches"""
    models = {
        'No Regularization': LinearRegression(learning_rate=0.01, n_iterations=1000),
        'L2 Regularization': LinearRegression(learning_rate=0.01, n_iterations=1000, 
                                           regularization='l2', lambda_param=0.1),
        'L1 Regularization': LinearRegression(learning_rate=0.01, n_iterations=1000, 
                                           regularization='l1', lambda_param=0.1)
    }
    
    print("\n=== Model Comparison ===")
    
    for name, model in models.items():
        # Train model
        model.fit(X_train, y_train)
        
        # Make predictions
        y_pred = model.predict(X_test)
        
        # Calculate metrics
        r2 = model.score(X_test, y_test)
        mse = model.mean_squared_error(X_test, y_test)
        mae = model.mean_absolute_error(X_test, y_test)
        
        print(f"\n{name}:")
        print(f"  R² Score: {r2:.4f}")
        print(f"  MSE: {mse:.4f}")
        print(f"  MAE: {mae:.4f}")
        print(f"  Weights: {model.weights}")

def main():
    """Main function to demonstrate Linear Regression"""
    print("=== Linear Regression Demo ===\n")
    
    # Simple linear regression
    print("1. Simple Linear Regression:")
    X_simple, y_simple = generate_sample_data(100, noise=0.2)
    
    # Split data
    split_idx = int(0.8 * len(X_simple))
    X_train, X_test = X_simple[:split_idx], X_simple[split_idx:]
    y_train, y_test = y_simple[:split_idx], y_simple[split_idx:]
    
    # Create and train model
    lr_simple = LinearRegression(learning_rate=0.01, n_iterations=1000)
    lr_simple.fit(X_train, y_train)
    
    # Make predictions
    y_pred_simple = lr_simple.predict(X_test)
    
    # Calculate metrics
    r2_simple = lr_simple.score(X_test, y_test)
    mse_simple = lr_simple.mean_squared_error(X_test, y_test)
    
    print(f"Simple Regression Results:")
    print(f"  R² Score: {r2_simple:.4f}")
    print(f"  MSE: {mse_simple:.4f}")
    print(f"  Weight: {lr_simple.weights[0]:.4f}")
    print(f"  Bias: {lr_simple.bias:.4f}")
    
    # Visualize simple regression
    visualize_results(X_test, y_test, y_pred_simple, lr_simple.cost_history, 
                     "Simple Linear Regression")
    
    # Multivariate linear regression
    print("\n2. Multivariate Linear Regression:")
    X_multi, y_multi = generate_multivariate_data(200, 3)
    
    # Split data
    split_idx = int(0.8 * len(X_multi))
    X_train_multi, X_test_multi = X_multi[:split_idx], X_multi[split_idx:]
    y_train_multi, y_test_multi = y_multi[:split_idx], y_multi[split_idx:]
    
    # Create and train model
    lr_multi = LinearRegression(learning_rate=0.01, n_iterations=1000)
    lr_multi.fit(X_train_multi, y_train_multi)
    
    # Make predictions
    y_pred_multi = lr_multi.predict(X_test_multi)
    
    # Calculate metrics
    r2_multi = lr_multi.score(X_test_multi, y_test_multi)
    mse_multi = lr_multi.mean_squared_error(X_test_multi, y_test_multi)
    
    print(f"Multivariate Regression Results:")
    print(f"  R² Score: {r2_multi:.4f}")
    print(f"  MSE: {mse_multi:.4f}")
    print(f"  Weights: {lr_multi.weights}")
    print(f"  Bias: {lr_multi.bias:.4f}")
    
    # Visualize multivariate regression
    visualize_multivariate_results(X_test_multi, y_test_multi, y_pred_multi, lr_multi.cost_history)
    
    # Compare different regularization approaches
    compare_models(X_train_multi, y_train_multi, X_test_multi, y_test_multi)
    
    # Save best model
    lr_multi.save_model('linear_regression_model.json')
    
    # Show sample predictions
    print("\nSample Predictions (Multivariate):")
    for i in range(5):
        actual = y_test_multi[i]
        predicted = y_pred_multi[i]
        error = abs(actual - predicted)
        print(f"Sample {i+1}: Actual={actual:.3f}, Predicted={predicted:.3f}, Error={error:.3f}")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Training: python linear_regression.py
2. Demonstrates both simple and multivariate regression
3. Compares different regularization approaches
4. Visualizes results and training progress
5. Saves model to linear_regression_model.json

Key Concepts:
- Linear Regression: Modeling linear relationships
- Gradient Descent: Optimization algorithm
- Regularization: L1 and L2 regularization
- Cost Function: Mean squared error
- Model Evaluation: R², MSE, MAE metrics

Applications:
- Predictive modeling
- Trend analysis
- Financial forecasting
- Sales prediction
- Scientific data analysis
- Feature importance analysis

Mathematical Foundation:
y = w₁x₁ + w₂x₂ + ... + wₙxₙ + b
Minimize: (1/2m) * Σ(y_pred - y)² + λ * regularization
"""
