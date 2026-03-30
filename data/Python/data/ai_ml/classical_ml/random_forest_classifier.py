"""
Random Forest Classifier
========================

Random Forest implementation from scratch using decision trees.
Demonstrates ensemble learning, bagging, and feature importance.
"""

import numpy as np
from typing import List, Tuple, Optional
import json
import matplotlib.pyplot as plt

class DecisionNode:
    """Decision tree node"""
    
    def __init__(self, feature_idx: int = None, threshold: float = None, 
                 left: 'DecisionNode' = None, right: 'DecisionNode' = None, 
                 value: Optional[float] = None, is_leaf: bool = False):
        self.feature_idx = feature_idx
        self.threshold = threshold
        self.left = left
        self.right = right
        self.value = value
        self.is_leaf = is_leaf

class DecisionTree:
    """Simple decision tree implementation"""
    
    def __init__(self, max_depth: int = 5, min_samples_split: int = 2):
        self.max_depth = max_depth
        self.min_samples_split = min_samples_split
        self.root = None
    
    def gini_impurity(self, y: np.ndarray) -> float:
        """Calculate Gini impurity"""
        if len(y) == 0:
            return 0
        
        p = np.bincount(y.astype(int)) / len(y)
        return 1 - np.sum(p ** 2)
    
    def split_data(self, X: np.ndarray, y: np.ndarray, feature_idx: int, threshold: float) -> Tuple:
        """Split data based on feature and threshold"""
        left_mask = X[:, feature_idx] <= threshold
        right_mask = X[:, feature_idx] > threshold
        
        left_X, left_y = X[left_mask], y[left_mask]
        right_X, right_y = X[right_mask], y[right_mask]
        
        return left_X, left_y, right_X, right_y
    
    def find_best_split(self, X: np.ndarray, y: np.ndarray) -> Tuple[int, float, float]:
        """Find the best split for the data"""
        best_feature, best_threshold = None, None
        best_gini = float('inf')
        
        n_features = X.shape[1]
        
        for feature_idx in range(n_features):
            feature_values = np.unique(X[:, feature_idx])
            
            for threshold in feature_values[:-1]:  # Skip last value to avoid empty split
                left_X, left_y, right_X, right_y = self.split_data(X, y, feature_idx, threshold)
                
                if len(left_y) == 0 or len(right_y) == 0:
                    continue
                
                # Calculate weighted Gini impurity
                n_left, n_right = len(left_y), len(right_y)
                n_total = len(y)
                gini_left = self.gini_impurity(left_y)
                gini_right = self.gini_impurity(right_y)
                weighted_gini = (n_left / n_total) * gini_left + (n_right / n_total) * gini_right
                
                if weighted_gini < best_gini:
                    best_gini = weighted_gini
                    best_feature = feature_idx
                    best_threshold = threshold
        
        return best_feature, best_threshold, best_gini
    
    def build_tree(self, X: np.ndarray, y: np.ndarray, depth: int = 0) -> DecisionNode:
        """Recursively build the decision tree"""
        n_samples, n_features = X.shape
        n_classes = len(np.unique(y))
        
        # Stopping conditions
        if (depth >= self.max_depth or n_classes == 1 or 
            n_samples < self.min_samples_split):
            leaf_value = np.bincount(y.astype(int)).argmax()
            return DecisionNode(value=leaf_value, is_leaf=True)
        
        # Find best split
        best_feature, best_threshold, _ = self.find_best_split(X, y)
        
        if best_feature is None:
            leaf_value = np.bincount(y.astype(int)).argmax()
            return DecisionNode(value=leaf_value, is_leaf=True)
        
        # Split data
        left_X, left_y, right_X, right_y = self.split_data(X, y, best_feature, best_threshold)
        
        # Build subtrees
        left_subtree = self.build_tree(left_X, left_y, depth + 1)
        right_subtree = self.build_tree(right_X, right_y, depth + 1)
        
        return DecisionNode(feature_idx=best_feature, threshold=best_threshold,
                       left=left_subtree, right=right_subtree)
    
    def fit(self, X: np.ndarray, y: np.ndarray) -> None:
        """Train the decision tree"""
        self.root = self.build_tree(X, y)
    
    def predict_sample(self, x: np.ndarray, node: DecisionNode) -> float:
        """Predict a single sample"""
        if node.is_leaf:
            return node.value
        
        if x[node.feature_idx] <= node.threshold:
            return self.predict_sample(x, node.left)
        else:
            return self.predict_sample(x, node.right)
    
    def predict(self, X: np.ndarray) -> np.ndarray:
        """Make predictions"""
        return np.array([self.predict_sample(x, self.root) for x in X])

class RandomForest:
    """Random Forest ensemble classifier"""
    
    def __init__(self, n_trees: int = 10, max_depth: int = 5, 
                 min_samples_split: int = 2, n_features: int = None):
        self.n_trees = n_trees
        self.max_depth = max_depth
        self.min_samples_split = min_samples_split
        self.n_features = n_features
        self.trees = []
        self.feature_importance = {}
    
    def bootstrap_sample(self, X: np.ndarray, y: np.ndarray) -> Tuple[np.ndarray, np.ndarray]:
        """Create bootstrap sample"""
        n_samples = len(X)
        indices = np.random.choice(n_samples, n_samples, replace=True)
        return X[indices], y[indices]
    
    def fit(self, X: np.ndarray, y: np.ndarray) -> None:
        """Train the random forest"""
        print(f"Training Random Forest with {self.n_trees} trees...")
        
        n_features = self.n_features or int(np.sqrt(X.shape[1]))
        
        # Train each tree
        for i in range(self.n_trees):
            # Bootstrap sample
            X_boot, y_boot = self.bootstrap_sample(X, y)
            
            # Random feature subset
            feature_indices = np.random.choice(X.shape[1], n_features, replace=False)
            X_boot_subset = X_boot[:, feature_indices]
            
            # Train decision tree
            tree = DecisionTree(max_depth=self.max_depth, 
                              min_samples_split=self.min_samples_split)
            tree.fit(X_boot_subset, y_boot)
            self.trees.append(tree)
            
            # Track feature usage
            for feature_idx in feature_indices:
                self.feature_importance[feature_idx] = self.feature_importance.get(feature_idx, 0) + 1
        
        # Normalize feature importance
        total_importance = sum(self.feature_importance.values())
        if total_importance > 0:
            self.feature_importance = {k: v/total_importance for k, v in self.feature_importance.items()}
    
    def predict(self, X: np.ndarray) -> np.ndarray:
        """Make predictions using majority voting"""
        predictions = np.array([tree.predict(X) for tree in self.trees])
        
        # Majority voting
        majority_votes = np.apply_along_axis(predictions, 1, 
                                       lambda x: np.bincount(x.astype(int)).argmax())
        
        return majority_votes
    
    def predict_proba(self, X: np.ndarray) -> np.ndarray:
        """Predict class probabilities"""
        predictions = np.array([tree.predict(X) for tree in self.trees])
        
        n_classes = len(np.unique(predictions))
        n_samples = X.shape[0]
        
        # Calculate probabilities
        probabilities = np.zeros((n_samples, n_classes))
        
        for i in range(n_samples):
            for class_idx in range(n_classes):
                probabilities[i, class_idx] = np.sum(predictions[i] == class_idx) / len(predictions[i])
        
        return probabilities
    
    def get_feature_importance(self) -> dict:
        """Get feature importance scores"""
        return dict(sorted(self.feature_importance.items(), key=lambda x: x[1], reverse=True))
    
    def save_model(self, filename: str) -> None:
        """Save model to file"""
        model_data = {
            'n_trees': self.n_trees,
            'max_depth': self.max_depth,
            'min_samples_split': self.min_samples_split,
            'feature_importance': self.feature_importance
        }
        
        with open(filename, 'w') as f:
            json.dump(model_data, f, indent=2)
        
        print(f"Model saved to {filename}")

def generate_sample_data(n_samples: int = 1000) -> Tuple[np.ndarray, np.ndarray]:
    """Generate sample classification data"""
    np.random.seed(42)
    
    # Generate two features
    X1 = np.random.normal(0, 1, n_samples)
    X2 = np.random.normal(0, 1, n_samples)
    
    # Create two classes with different distributions
    class0_mask = np.random.rand(n_samples) < 0.5
    class1_mask = ~class0_mask
    
    X1[class0_mask] = np.random.normal(2, 1, np.sum(class0_mask))
    X2[class0_mask] = np.random.normal(2, 1, np.sum(class0_mask))
    
    X1[class1_mask] = np.random.normal(-2, 1, np.sum(class1_mask))
    X2[class1_mask] = np.random.normal(-2, 1, np.sum(class1_mask))
    
    X = np.column_stack([X1, X2])
    y = np.where(class0_mask, 0, 1)
    
    return X, y

def calculate_metrics(y_true: np.ndarray, y_pred: np.ndarray) -> dict:
    """Calculate classification metrics"""
    accuracy = np.mean(y_true == y_pred)
    
    # Calculate precision, recall, F1 for each class
    classes = np.unique(y_true)
    metrics = {}
    
    for cls in classes:
        tp = np.sum((y_true == cls) & (y_pred == cls))
        fp = np.sum((y_true != cls) & (y_pred == cls))
        fn = np.sum((y_true == cls) & (y_pred != cls))
        
        precision = tp / (tp + fp) if (tp + fp) > 0 else 0
        recall = tp / (tp + fn) if (tp + fn) > 0 else 0
        f1 = 2 * precision * recall / (precision + recall) if (precision + recall) > 0 else 0
        
        metrics[cls] = {'precision': precision, 'recall': recall, 'f1': f1}
    
    metrics['accuracy'] = accuracy
    return metrics

def main():
    """Main function to demonstrate Random Forest"""
    print("=== Random Forest Classifier Demo ===\n")
    
    # Generate sample data
    X_train, y_train = generate_sample_data(800)
    X_test, y_test = generate_sample_data(200)
    
    print(f"Training data shape: {X_train.shape}")
    print(f"Test data shape: {X_test.shape}")
    print(f"Class distribution: {np.bincount(y_train)}")
    
    # Create and train Random Forest
    rf = RandomForest(n_trees=20, max_depth=5, min_samples_split=2)
    rf.fit(X_train, y_train)
    
    # Make predictions
    predictions = rf.predict(X_test)
    probabilities = rf.predict_proba(X_test)
    
    # Calculate metrics
    metrics = calculate_metrics(y_test, predictions)
    
    print(f"\nTraining completed!")
    print(f"Test Accuracy: {metrics['accuracy']:.3f}")
    
    # Print per-class metrics
    for cls, cls_metrics in metrics.items():
        if isinstance(cls_metrics, dict):
            print(f"Class {cls}: Precision={cls_metrics['precision']:.3f}, "
                  f"Recall={cls_metrics['recall']:.3f}, F1={cls_metrics['f1']:.3f}")
    
    # Show feature importance
    feature_importance = rf.get_feature_importance()
    print(f"\nFeature Importance:")
    for feature, importance in feature_importance.items():
        print(f"Feature {feature}: {importance:.4f}")
    
    # Save model
    rf.save_model('random_forest_model.json')
    
    # Show sample predictions with probabilities
    print("\nSample predictions with probabilities:")
    for i in range(5):
        pred_class = predictions[i]
        pred_proba = probabilities[i]
        true_class = y_test[i]
        confidence = pred_proba[pred_class]
        status = "✓" if pred_class == true_class else "✗"
        print(f"Sample {i+1}: True={true_class}, Predicted={pred_class}, "
              f"Confidence={confidence:.3f}, {status}")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Training: python random_forest_classifier.py
2. The Random Forest will train on classification data
3. Calculates classification metrics and feature importance
4. Saves model to random_forest_model.json

Key Concepts:
- Decision Trees: Recursive partitioning based on Gini impurity
- Bootstrap Sampling: Random sampling with replacement
- Feature Bagging: Random feature subset for each tree
- Ensemble Voting: Majority voting across trees
- Feature Importance: Frequency of feature usage in splits

Applications:
- Classification tasks
- Feature selection
- Anomaly detection
- Medical diagnosis
- Financial risk assessment
- Customer churn prediction
"""
