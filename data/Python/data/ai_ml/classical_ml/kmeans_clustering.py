"""
K-Means Clustering Implementation
=================================

K-Means clustering algorithm from scratch.
Demonstrates unsupervised learning, clustering, and convergence analysis.
"""

import numpy as np
import matplotlib.pyplot as plt
from typing import List, Tuple, Optional
import json
import random

class KMeans:
    """K-Means clustering implementation"""
    
    def __init__(self, n_clusters: int = 3, max_iterations: int = 100, 
                 tolerance: float = 1e-4, random_state: int = None):
        self.n_clusters = n_clusters
        self.max_iterations = max_iterations
        self.tolerance = tolerance
        self.random_state = random_state
        
        self.centroids = None
        self.labels = None
        self.inertia_history = []
        self.converged = False
    
    def initialize_centroids(self, X: np.ndarray) -> np.ndarray:
        """Initialize centroids using k-means++ algorithm"""
        n_samples, n_features = X.shape
        
        if self.random_state is not None:
            np.random.seed(self.random_state)
        
        # K-means++ initialization
        centroids = []
        
        # Choose first centroid randomly
        first_idx = np.random.choice(n_samples)
        centroids.append(X[first_idx])
        
        # Choose remaining centroids
        for _ in range(1, self.n_clusters):
            # Calculate distances to nearest centroid
            distances = np.zeros(n_samples)
            
            for i in range(n_samples):
                min_dist = float('inf')
                for centroid in centroids:
                    dist = np.linalg.norm(X[i] - centroid)
                    min_dist = min(min_dist, dist)
                distances[i] = min_dist
            
            # Choose next centroid with probability proportional to distance²
            probabilities = distances ** 2
            probabilities = probabilities / np.sum(probabilities)
            
            next_idx = np.random.choice(n_samples, p=probabilities)
            centroids.append(X[next_idx])
        
        return np.array(centroids)
    
    def assign_clusters(self, X: np.ndarray, centroids: np.ndarray) -> np.ndarray:
        """Assign each data point to the nearest centroid"""
        n_samples = X.shape[0]
        labels = np.zeros(n_samples, dtype=int)
        
        for i in range(n_samples):
            distances = np.linalg.norm(X[i] - centroids, axis=1)
            labels[i] = np.argmin(distances)
        
        return labels
    
    def update_centroids(self, X: np.ndarray, labels: np.ndarray) -> np.ndarray:
        """Update centroids based on cluster assignments"""
        new_centroids = np.zeros((self.n_clusters, X.shape[1]))
        
        for k in range(self.n_clusters):
            cluster_points = X[labels == k]
            
            if len(cluster_points) > 0:
                new_centroids[k] = np.mean(cluster_points, axis=0)
            else:
                # If no points assigned to cluster, keep old centroid
                new_centroids[k] = self.centroids[k]
        
        return new_centroids
    
    def calculate_inertia(self, X: np.ndarray, labels: np.ndarray, centroids: np.ndarray) -> float:
        """Calculate within-cluster sum of squares (inertia)"""
        inertia = 0.0
        
        for k in range(self.n_clusters):
            cluster_points = X[labels == k]
            if len(cluster_points) > 0:
                inertia += np.sum((cluster_points - centroids[k]) ** 2)
        
        return inertia
    
    def fit(self, X: np.ndarray) -> None:
        """Fit K-Means clustering to the data"""
        print(f"Training K-Means with {self.n_clusters} clusters...")
        
        # Initialize centroids
        self.centroids = self.initialize_centroids(X)
        
        # Iterative optimization
        for iteration in range(self.max_iterations):
            # Assign clusters
            old_labels = self.labels.copy() if self.labels is not None else None
            self.labels = self.assign_clusters(X, self.centroids)
            
            # Update centroids
            old_centroids = self.centroids.copy()
            self.centroids = self.update_centroids(X, self.labels)
            
            # Calculate and store inertia
            inertia = self.calculate_inertia(X, self.labels, self.centroids)
            self.inertia_history.append(inertia)
            
            # Check for convergence
            if old_labels is not None:
                label_changes = np.sum(self.labels != old_labels)
                centroid_shift = np.max(np.linalg.norm(self.centroids - old_centroids, axis=1))
                
                if label_changes == 0 or centroid_shift < self.tolerance:
                    self.converged = True
                    print(f"Converged at iteration {iteration + 1}")
                    break
            
            if iteration % 10 == 0:
                print(f"Iteration {iteration + 1}, Inertia: {inertia:.4f}")
        
        if not self.converged:
            print("Reached maximum iterations without convergence")
        
        print("Training completed!")
    
    def predict(self, X: np.ndarray) -> np.ndarray:
        """Assign new data points to clusters"""
        if self.centroids is None:
            raise ValueError("Model not fitted yet. Call fit() first.")
        
        return self.assign_clusters(X, self.centroids)
    
    def predict_proba(self, X: np.ndarray) -> np.ndarray:
        """Get probability-like scores for each cluster"""
        if self.centroids is None:
            raise ValueError("Model not fitted yet. Call fit() first.")
        
        n_samples = X.shape[0]
        probabilities = np.zeros((n_samples, self.n_clusters))
        
        for i in range(n_samples):
            distances = np.linalg.norm(X[i] - self.centroids, axis=1)
            # Convert distances to probabilities (inverse distance)
            inv_distances = 1 / (distances + 1e-8)
            probabilities[i] = inv_distances / np.sum(inv_distances)
        
        return probabilities
    
    def get_cluster_sizes(self) -> np.ndarray:
        """Get the size of each cluster"""
        if self.labels is None:
            return np.array([])
        
        sizes = np.zeros(self.n_clusters, dtype=int)
        for k in range(self.n_clusters):
            sizes[k] = np.sum(self.labels == k)
        
        return sizes
    
    def get_cluster_centers(self) -> np.ndarray:
        """Get cluster centers"""
        return self.centroids.copy() if self.centroids is not None else None
    
    def save_model(self, filename: str) -> None:
        """Save model to file"""
        model_data = {
            'n_clusters': self.n_clusters,
            'centroids': self.centroids.tolist() if self.centroids is not None else None,
            'inertia_history': self.inertia_history,
            'converged': self.converged
        }
        
        with open(filename, 'w') as f:
            json.dump(model_data, f, indent=2)
        
        print(f"Model saved to {filename}")

def generate_sample_data(n_samples: int = 300, n_clusters: int = 3, random_state: int = 42) -> np.ndarray:
    """Generate sample clustering data"""
    np.random.seed(random_state)
    
    # Generate cluster centers
    centers = np.random.randn(n_clusters, 2) * 3
    
    # Generate data points around each center
    data = []
    points_per_cluster = n_samples // n_clusters
    
    for center in centers:
        # Generate points around center with some noise
        cluster_data = np.random.randn(points_per_cluster, 2) + center
        data.append(cluster_data)
    
    # Combine all clusters
    X = np.vstack(data)
    
    return X, centers

def generate_moons_data(n_samples: int = 200) -> np.ndarray:
    """Generate moon-shaped data for clustering"""
    from sklearn.datasets import make_moons
    
    X, _ = make_moons(n_samples=n_samples, noise=0.1, random_state=42)
    return X

def generate_circles_data(n_samples: int = 200) -> np.ndarray:
    """Generate circular data for clustering"""
    from sklearn.datasets import make_circles
    
    X, _ = make_circles(n_samples=n_samples, noise=0.05, random_state=42)
    return X

def visualize_clustering_results(X: np.ndarray, labels: np.ndarray, centroids: np.ndarray, 
                                title: str = "K-Means Clustering") -> None:
    """Visualize clustering results"""
    plt.figure(figsize=(12, 5))
    
    # Plot clustering results
    plt.subplot(1, 2, 1)
    colors = plt.cm.Set3(np.linspace(0, 1, len(np.unique(labels))))
    
    for k, color in zip(np.unique(labels), colors):
        cluster_points = X[labels == k]
        plt.scatter(cluster_points[:, 0], cluster_points[:, 1], 
                   c=[color], label=f'Cluster {k}', alpha=0.7)
    
    # Plot centroids
    plt.scatter(centroids[:, 0], centroids[:, 1], 
               c='black', marker='x', s=200, linewidths=3, label='Centroids')
    
    plt.title(title)
    plt.xlabel('Feature 1')
    plt.ylabel('Feature 2')
    plt.legend()
    plt.grid(True)
    
    # Plot inertia history
    plt.subplot(1, 2, 2)
    plt.plot(range(len(kmeans.inertia_history)), kmeans.inertia_history, 'b-')
    plt.xlabel('Iteration')
    plt.ylabel('Inertia')
    plt.title('Convergence History')
    plt.grid(True)
    
    plt.tight_layout()
    plt.show()

def find_optimal_k(X: np.ndarray, max_k: int = 10) -> Tuple[List[int], List[float]]:
    """Find optimal number of clusters using elbow method"""
    inertias = []
    k_values = list(range(1, max_k + 1))
    
    print("Finding optimal number of clusters...")
    
    for k in k_values:
        kmeans = KMeans(n_clusters=k, max_iterations=100, random_state=42)
        kmeans.fit(X)
        inertias.append(kmeans.inertia_history[-1])
        print(f"K={k}, Inertia={kmeans.inertia_history[-1]:.4f}")
    
    # Plot elbow curve
    plt.figure(figsize=(10, 6))
    plt.plot(k_values, inertias, 'bo-')
    plt.xlabel('Number of Clusters (K)')
    plt.ylabel('Inertia')
    plt.title('Elbow Method for Optimal K')
    plt.grid(True)
    
    # Find elbow point (simple heuristic)
    if len(inertias) > 2:
        # Calculate angles between consecutive points
        angles = []
        for i in range(1, len(k_values) - 1):
            v1 = np.array([k_values[i-1], inertias[i-1]])
            v2 = np.array([k_values[i], inertias[i]])
            v3 = np.array([k_values[i+1], inertias[i+1]])
            
            # Calculate angle
            vec1 = v2 - v1
            vec2 = v3 - v2
            
            cos_angle = np.dot(vec1, vec2) / (np.linalg.norm(vec1) * np.linalg.norm(vec2))
            angle = np.arccos(np.clip(cos_angle, -1, 1))
            angles.append(angle)
        
        # Find point with maximum angle (elbow)
        if angles:
            optimal_k_idx = np.argmax(angles) + 1  # +1 because we start from k=2
            optimal_k = k_values[optimal_k_idx]
            
            plt.axvline(x=optimal_k, color='r', linestyle='--', 
                       label=f'Optimal K = {optimal_k}')
            plt.legend()
        
        plt.show()
        
        return k_values, inertias
    else:
        plt.show()
        return k_values, inertias

def analyze_clusters(X: np.ndarray, labels: np.ndarray, centroids: np.ndarray) -> dict:
    """Analyze cluster characteristics"""
    analysis = {}
    
    for k in range(len(centroids)):
        cluster_points = X[labels == k]
        
        if len(cluster_points) > 0:
            analysis[f'Cluster {k}'] = {
                'size': len(cluster_points),
                'centroid': centroids[k],
                'mean_distance_to_centroid': np.mean(np.linalg.norm(cluster_points - centroids[k], axis=1)),
                'std_distance_to_centroid': np.std(np.linalg.norm(cluster_points - centroids[k], axis=1)),
                'feature_ranges': {
                    'min': np.min(cluster_points, axis=0),
                    'max': np.max(cluster_points, axis=0),
                    'mean': np.mean(cluster_points, axis=0),
                    'std': np.std(cluster_points, axis=0)
                }
            }
    
    return analysis

def main():
    """Main function to demonstrate K-Means clustering"""
    print("=== K-Means Clustering Demo ===\n")
    
    # Generate sample data
    X, true_centers = generate_sample_data(n_samples=300, n_clusters=3)
    
    print(f"Generated {len(X)} data points with {len(true_centers)} true clusters")
    print(f"True cluster centers: {true_centers}")
    
    # Find optimal number of clusters
    k_values, inertias = find_optimal_k(X, max_k=8)
    
    # Train K-Means with optimal K (or use K=3 for demonstration)
    optimal_k = 3  # You can change this based on elbow method results
    global kmeans
    kmeans = KMeans(n_clusters=optimal_k, max_iterations=100, random_state=42)
    kmeans.fit(X)
    
    # Get results
    labels = kmeans.labels
    centroids = kmeans.centroids
    cluster_sizes = kmeans.get_cluster_sizes()
    
    print(f"\nClustering Results:")
    print(f"Optimal K: {optimal_k}")
    print(f"Final inertia: {kmeans.inertia_history[-1]:.4f}")
    print(f"Converged: {kmeans.converged}")
    print(f"Cluster sizes: {cluster_sizes}")
    print(f"Final centroids: {centroids}")
    
    # Visualize results
    visualize_clustering_results(X, labels, centroids, f"K-Means (K={optimal_k})")
    
    # Analyze clusters
    analysis = analyze_clusters(X, labels, centroids)
    
    print(f"\nCluster Analysis:")
    for cluster_name, cluster_info in analysis.items():
        print(f"\n{cluster_name}:")
        print(f"  Size: {cluster_info['size']}")
        print(f"  Centroid: {cluster_info['centroid']}")
        print(f"  Mean distance to centroid: {cluster_info['mean_distance_to_centroid']:.4f}")
        print(f"  Std distance to centroid: {cluster_info['std_distance_to_centroid']:.4f}")
    
    # Test with different datasets
    print("\n=== Testing with Different Datasets ===")
    
    # Moons dataset
    print("\n1. Moons Dataset:")
    X_moons = generate_moons_data(200)
    kmeans_moons = KMeans(n_clusters=2, max_iterations=100, random_state=42)
    kmeans_moons.fit(X_moons)
    
    visualize_clustering_results(X_moons, kmeans_moons.labels, kmeans_moons.centroids, 
                                "K-Means on Moons Dataset")
    
    # Circles dataset
    print("\n2. Circles Dataset:")
    X_circles = generate_circles_data(200)
    kmeans_circles = KMeans(n_clusters=2, max_iterations=100, random_state=42)
    kmeans_circles.fit(X_circles)
    
    visualize_clustering_results(X_circles, kmeans_circles.labels, kmeans_circles.centroids, 
                                "K-Means on Circles Dataset")
    
    # Save model
    kmeans.save_model('kmeans_model.json')
    
    # Show sample predictions
    print("\nSample Predictions:")
    for i in range(5):
        point = X[i]
        cluster = labels[i]
        centroid = centroids[cluster]
        distance = np.linalg.norm(point - centroid)
        print(f"Point {i+1}: {point}, Cluster: {cluster}, Distance to centroid: {distance:.4f}")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Training: python kmeans_clustering.py
2. Demonstrates K-Means on different datasets
3. Finds optimal number of clusters using elbow method
4. Visualizes clustering results and convergence
5. Saves model to kmeans_model.json

Key Concepts:
- Unsupervised Learning: Learning without labeled data
- Clustering: Grouping similar data points
- K-Means Algorithm: Iterative centroid-based clustering
- K-Means++ Initialization: Smart centroid initialization
- Inertia: Within-cluster sum of squares
- Elbow Method: Finding optimal number of clusters

Applications:
- Customer segmentation
- Image compression
- Anomaly detection
- Document clustering
- Market segmentation
- Social network analysis

Algorithm Steps:
1. Initialize K centroids (K-means++)
2. Assign each point to nearest centroid
3. Update centroids as mean of assigned points
4. Repeat until convergence

Limitations:
- Assumes spherical clusters
- Sensitive to initialization
- Requires specifying K
- Struggles with non-convex shapes
"""
