"""
Machine Learning with Scikit-Learn
Complete ML pipeline with various algorithms and model evaluation.
"""

import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.datasets import make_classification, make_regression, load_iris, load_boston
from sklearn.model_selection import train_test_split, cross_val_score, GridSearchCV
from sklearn.preprocessing import StandardScaler, MinMaxScaler, LabelEncoder
from sklearn.feature_selection import SelectKBest, f_classif, RFE
from sklearn.metrics import (
    accuracy_score, precision_score, recall_score, f1_score,
    mean_squared_error, mean_absolute_error, r2_score,
    classification_report, confusion_matrix, roc_auc_score, roc_curve
)
from sklearn.linear_model import LinearRegression, LogisticRegression, Ridge, Lasso
from sklearn.tree import DecisionTreeClassifier, DecisionTreeRegressor
from sklearn.ensemble import RandomForestClassifier, RandomForestRegressor, GradientBoostingClassifier
from sklearn.svm import SVC, SVR
from sklearn.neighbors import KNeighborsClassifier, KNeighborsRegressor
from sklearn.naive_bayes import GaussianNB
from sklearn.cluster import KMeans, DBSCAN, AgglomerativeClustering
from sklearn.decomposition import PCA, TSNE
import warnings
warnings.filterwarnings('ignore')

class MLPipeline:
    """Complete machine learning pipeline."""
    
    def __init__(self):
        """Initialize the ML pipeline."""
        self.scalers = {
            'standard': StandardScaler(),
            'minmax': MinMaxScaler()
        }
        self.models = {}
        self.results = {}
        self.best_model = None
        self.best_score = 0
    
    def create_classification_dataset(self, n_samples=1000, n_features=20, n_classes=3):
        """Create a synthetic classification dataset."""
        X, y = make_classification(
            n_samples=n_samples,
            n_features=n_features,
            n_informative=max(2, n_features // 3),
            n_redundant=max(1, n_features // 6),
            n_classes=n_classes,
            random_state=42
        )
        
        feature_names = [f'feature_{i}' for i in range(n_features)]
        self.X = pd.DataFrame(X, columns=feature_names)
        self.y = pd.Series(y, name='target')
        
        print(f"Classification dataset created: {self.X.shape}")
        print(f"Classes: {np.unique(self.y)}")
        print(f"Class distribution: {np.bincount(self.y)}")
    
    def create_regression_dataset(self, n_samples=1000, n_features=15):
        """Create a synthetic regression dataset."""
        X, y = make_regression(
            n_samples=n_samples,
            n_features=n_features,
            n_informative=max(2, n_features // 2),
            noise=0.1,
            random_state=42
        )
        
        feature_names = [f'feature_{i}' for i in range(n_features)]
        self.X = pd.DataFrame(X, columns=feature_names)
        self.y = pd.Series(y, name='target')
        
        print(f"Regression dataset created: {self.X.shape}")
        print(f"Target range: [{self.y.min():.2f}, {self.y.max():.2f}]")
    
    def load_real_dataset(self, dataset_name='iris'):
        """Load a real dataset."""
        if dataset_name == 'iris':
            data = load_iris()
            self.X = pd.DataFrame(data.data, columns=data.feature_names)
            self.y = pd.Series(data.target, name='target')
            print(f"Iris dataset loaded: {self.X.shape}")
            print(f"Classes: {data.target_names}")
        
        elif dataset_name == 'boston':
            data = load_boston()
            self.X = pd.DataFrame(data.data, columns=data.feature_names)
            self.y = pd.Series(data.target, name='target')
            print(f"Boston housing dataset loaded: {self.X.shape}")
        
        else:
            raise ValueError(f"Unknown dataset: {dataset_name}")
    
    def preprocess_data(self, test_size=0.2, scaler_type='standard'):
        """Preprocess the data."""
        # Split data
        self.X_train, self.X_test, self.y_train, self.y_test = train_test_split(
            self.X, self.y, test_size=test_size, random_state=42, stratify=self.y
        )
        
        # Scale features
        scaler = self.scalers[scaler_type]
        self.X_train_scaled = scaler.fit_transform(self.X_train)
        self.X_test_scaled = scaler.transform(self.X_test)
        
        print(f"Data split: {len(self.X_train)} train, {len(self.X_test)} test")
        print(f"Features scaled using {scaler_type} scaler")
    
    def feature_selection(self, method='selectkbest', k=10):
        """Perform feature selection."""
        if method == 'selectkbest':
            selector = SelectKBest(score_func=f_classif, k=k)
            self.X_train_selected = selector.fit_transform(self.X_train_scaled, self.y_train)
            self.X_test_selected = selector.transform(self.X_test_scaled)
            
            # Get selected feature names
            selected_features = selector.get_support(indices=True)
            self.selected_feature_names = self.X.columns[selected_features].tolist()
            
            print(f"Selected {k} best features: {self.selected_feature_names}")
        
        elif method == 'rfe':
            # Use a simple estimator for RFE
            estimator = LogisticRegression(random_state=42)
            selector = RFE(estimator, n_features_to_select=k)
            self.X_train_selected = selector.fit_transform(self.X_train_scaled, self.y_train)
            self.X_test_selected = selector.transform(self.X_test_scaled)
            
            selected_features = selector.get_support(indices=True)
            self.selected_feature_names = self.X.columns[selected_features].tolist()
            
            print(f"Selected {k} features using RFE: {self.selected_feature_names}")
        
        return self.X_train_selected, self.X_test_selected
    
    def train_classification_models(self):
        """Train multiple classification models."""
        classification_models = {
            'Logistic Regression': LogisticRegression(random_state=42),
            'Decision Tree': DecisionTreeClassifier(random_state=42),
            'Random Forest': RandomForestClassifier(random_state=42),
            'Gradient Boosting': GradientBoostingClassifier(random_state=42),
            'SVM': SVC(random_state=42, probability=True),
            'KNN': KNeighborsClassifier(),
            'Naive Bayes': GaussianNB()
        }
        
        print("\nTraining Classification Models:")
        print("-" * 40)
        
        for name, model in classification_models.items():
            # Train model
            model.fit(self.X_train_selected, self.y_train)
            
            # Make predictions
            y_pred = model.predict(self.X_test_selected)
            y_prob = model.predict_proba(self.X_test_selected) if hasattr(model, 'predict_proba') else None
            
            # Calculate metrics
            accuracy = accuracy_score(self.y_test, y_pred)
            precision = precision_score(self.y_test, y_pred, average='weighted', zero_division=0)
            recall = recall_score(self.y_test, y_pred, average='weighted', zero_division=0)
            f1 = f1_score(self.y_test, y_pred, average='weighted', zero_division=0)
            
            # Cross-validation
            cv_scores = cross_val_score(model, self.X_train_selected, self.y_train, cv=5)
            
            # Store results
            self.results[name] = {
                'model': model,
                'accuracy': accuracy,
                'precision': precision,
                'recall': recall,
                'f1': f1,
                'cv_mean': cv_scores.mean(),
                'cv_std': cv_scores.std(),
                'predictions': y_pred,
                'probabilities': y_prob
            }
            
            print(f"{name}:")
            print(f"  Accuracy: {accuracy:.3f} ± {cv_scores.std():.3f}")
            print(f"  F1-Score: {f1:.3f}")
            
            # Update best model
            if accuracy > self.best_score:
                self.best_score = accuracy
                self.best_model = model
                self.best_model_name = name
        
        print(f"\nBest Model: {self.best_model_name} (Accuracy: {self.best_score:.3f})")
    
    def train_regression_models(self):
        """Train multiple regression models."""
        regression_models = {
            'Linear Regression': LinearRegression(),
            'Ridge Regression': Ridge(random_state=42),
            'Lasso Regression': Lasso(random_state=42),
            'Decision Tree': DecisionTreeRegressor(random_state=42),
            'Random Forest': RandomForestRegressor(random_state=42),
            'SVR': SVR(),
            'KNN': KNeighborsRegressor()
        }
        
        print("\nTraining Regression Models:")
        print("-" * 35)
        
        for name, model in regression_models.items():
            # Train model
            model.fit(self.X_train_selected, self.y_train)
            
            # Make predictions
            y_pred = model.predict(self.X_test_selected)
            
            # Calculate metrics
            mse = mean_squared_error(self.y_test, y_pred)
            mae = mean_absolute_error(self.y_test, y_pred)
            r2 = r2_score(self.y_test, y_pred)
            
            # Cross-validation
            cv_scores = cross_val_score(model, self.X_train_selected, self.y_train, cv=5, scoring='r2')
            
            # Store results
            self.results[name] = {
                'model': model,
                'mse': mse,
                'mae': mae,
                'r2': r2,
                'cv_mean': cv_scores.mean(),
                'cv_std': cv_scores.std(),
                'predictions': y_pred
            }
            
            print(f"{name}:")
            print(f"  R²: {r2:.3f} ± {cv_scores.std():.3f}")
            print(f"  MSE: {mse:.3f}")
            
            # Update best model
            if r2 > self.best_score:
                self.best_score = r2
                self.best_model = model
                self.best_model_name = name
        
        print(f"\nBest Model: {self.best_model_name} (R²: {self.best_score:.3f})")
    
    def hyperparameter_tuning(self, model_name='Random Forest'):
        """Perform hyperparameter tuning for the best model."""
        if model_name not in self.results:
            print(f"Model {model_name} not found!")
            return
        
        model = self.results[model_name]['model']
        
        # Define parameter grid based on model type
        if 'Forest' in model_name:
            param_grid = {
                'n_estimators': [50, 100, 200],
                'max_depth': [None, 10, 20],
                'min_samples_split': [2, 5, 10]
            }
        elif 'Tree' in model_name:
            param_grid = {
                'max_depth': [None, 10, 20, 30],
                'min_samples_split': [2, 5, 10],
                'min_samples_leaf': [1, 2, 4]
            }
        elif 'SVM' in model_name:
            param_grid = {
                'C': [0.1, 1, 10],
                'kernel': ['rbf', 'linear'],
                'gamma': ['scale', 'auto']
            }
        else:
            print("No parameter grid defined for this model type")
            return
        
        # Perform grid search
        grid_search = GridSearchCV(
            model, param_grid, cv=5, scoring='accuracy', n_jobs=-1
        )
        grid_search.fit(self.X_train_selected, self.y_train)
        
        # Update best model
        self.best_model = grid_search.best_estimator_
        self.best_score = grid_search.best_score_
        
        print(f"\nHyperparameter Tuning Results:")
        print(f"Best Parameters: {grid_search.best_params_}")
        print(f"Best Cross-Validation Score: {grid_search.best_score_:.3f}")
    
    def evaluate_model(self, model_name=None):
        """Evaluate model performance with detailed metrics."""
        if model_name is None:
            model_name = self.best_model_name
        
        if model_name not in self.results:
            print(f"Model {model_name} not found!")
            return
        
        result = self.results[model_name]
        model = result['model']
        
        print(f"\nDetailed Evaluation - {model_name}:")
        print("-" * 50)
        
        # Check if it's classification or regression
        if 'accuracy' in result:
            self._evaluate_classification(model_name, result)
        else:
            self._evaluate_regression(model_name, result)
    
    def _evaluate_classification(self, model_name, result):
        """Evaluate classification model."""
        y_pred = result['predictions']
        y_prob = result['probabilities']
        
        # Classification report
        print("Classification Report:")
        print(classification_report(self.y_test, y_pred))
        
        # Confusion matrix
        cm = confusion_matrix(self.y_test, y_pred)
        plt.figure(figsize=(8, 6))
        sns.heatmap(cm, annot=True, fmt='d', cmap='Blues')
        plt.title(f'Confusion Matrix - {model_name}')
        plt.ylabel('True Label')
        plt.xlabel('Predicted Label')
        plt.show()
        
        # ROC curve (if binary classification)
        if len(np.unique(self.y)) == 2 and y_prob is not None:
            fpr, tpr, _ = roc_curve(self.y_test, y_prob[:, 1])
            auc = roc_auc_score(self.y_test, y_prob[:, 1])
            
            plt.figure(figsize=(8, 6))
            plt.plot(fpr, tpr, label=f'ROC Curve (AUC = {auc:.3f})')
            plt.plot([0, 1], [0, 1], 'k--')
            plt.xlabel('False Positive Rate')
            plt.ylabel('True Positive Rate')
            plt.title(f'ROC Curve - {model_name}')
            plt.legend()
            plt.show()
    
    def _evaluate_regression(self, model_name, result):
        """Evaluate regression model."""
        y_pred = result['predictions']
        
        # Metrics
        mse = mean_squared_error(self.y_test, y_pred)
        mae = mean_absolute_error(self.y_test, y_pred)
        r2 = r2_score(self.y_test, y_pred)
        
        print(f"Mean Squared Error: {mse:.3f}")
        print(f"Mean Absolute Error: {mae:.3f}")
        print(f"R² Score: {r2:.3f}")
        
        # Actual vs Predicted plot
        plt.figure(figsize=(10, 6))
        plt.scatter(self.y_test, y_pred, alpha=0.7)
        plt.plot([self.y_test.min(), self.y_test.max()], 
                [self.y_test.min(), self.y_test.max()], 'r--', lw=2)
        plt.xlabel('Actual')
        plt.ylabel('Predicted')
        plt.title(f'Actual vs Predicted - {model_name}')
        plt.grid(True, alpha=0.3)
        plt.show()
        
        # Residual plot
        residuals = self.y_test - y_pred
        plt.figure(figsize=(10, 6))
        plt.scatter(y_pred, residuals, alpha=0.7)
        plt.axhline(y=0, color='r', linestyle='--')
        plt.xlabel('Predicted')
        plt.ylabel('Residuals')
        plt.title(f'Residual Plot - {model_name}')
        plt.grid(True, alpha=0.3)
        plt.show()
    
    def perform_clustering(self, n_clusters=3):
        """Perform clustering analysis."""
        clustering_models = {
            'K-Means': KMeans(n_clusters=n_clusters, random_state=42),
            'DBSCAN': DBSCAN(eps=0.5, min_samples=5),
            'Agglomerative': AgglomerativeClustering(n_clusters=n_clusters)
        }
        
        print("\nClustering Analysis:")
        print("-" * 25)
        
        for name, model in clustering_models.items():
            # Fit model
            cluster_labels = model.fit_predict(self.X_train_scaled)
            
            # Calculate silhouette score (if not DBSCAN with noise)
            if len(set(cluster_labels)) > 1:
                from sklearn.metrics import silhouette_score
                silhouette = silhouette_score(self.X_train_scaled, cluster_labels)
                print(f"{name}: Silhouette Score = {silhouette:.3f}")
            else:
                print(f"{name}: Single cluster or noise detected")
            
            # Visualize clusters (using PCA if needed)
            if self.X_train_scaled.shape[1] > 2:
                pca = PCA(n_components=2)
                X_pca = pca.fit_transform(self.X_train_scaled)
                
                plt.figure(figsize=(10, 8))
                scatter = plt.scatter(X_pca[:, 0], X_pca[:, 1], c=cluster_labels, cmap='viridis', alpha=0.7)
                plt.colorbar(scatter)
                plt.title(f'{name} Clustering (PCA Visualization)')
                plt.xlabel('PC1')
                plt.ylabel('PC2')
                plt.show()
    
    def dimensionality_reduction(self, method='pca', n_components=2):
        """Perform dimensionality reduction."""
        if method == 'pca':
            reducer = PCA(n_components=n_components)
        elif method == 'tsne':
            reducer = TSNE(n_components=n_components, random_state=42)
        else:
            raise ValueError(f"Unknown method: {method}")
        
        # Fit and transform
        X_reduced = reducer.fit_transform(self.X_train_scaled)
        
        # Visualize
        plt.figure(figsize=(10, 8))
        scatter = plt.scatter(X_reduced[:, 0], X_reduced[:, 1], 
                            c=self.y_train, cmap='viridis', alpha=0.7)
        plt.colorbar(scatter)
        plt.title(f'{method.upper()} Visualization')
        plt.xlabel(f'{method.upper()} 1')
        plt.ylabel(f'{method.upper()} 2')
        plt.show()
        
        # Print explained variance (for PCA)
        if method == 'pca':
            print(f"Explained variance ratio: {reducer.explained_variance_ratio_}")
            print(f"Total explained variance: {reducer.explained_variance_ratio_.sum():.3f}")
    
    def generate_report(self):
        """Generate a comprehensive ML report."""
        report = []
        report.append("MACHINE LEARNING PIPELINE REPORT")
        report.append("="*50)
        report.append(f"Dataset Shape: {self.X.shape}")
        report.append(f"Features: {len(self.selected_feature_names)}")
        report.append(f"Task Type: {'Classification' if len(np.unique(self.y)) < 20 else 'Regression'}")
        report.append("")
        
        report.append("MODEL PERFORMANCE:")
        for name, result in self.results.items():
            if 'accuracy' in result:
                report.append(f"{name}:")
                report.append(f"  Accuracy: {result['accuracy']:.3f}")
                report.append(f"  F1-Score: {result['f1']:.3f}")
                report.append(f"  CV Score: {result['cv_mean']:.3f} ± {result['cv_std']:.3f}")
            else:
                report.append(f"{name}:")
                report.append(f"  R²: {result['r2']:.3f}")
                report.append(f"  MSE: {result['mse']:.3f}")
                report.append(f"  CV Score: {result['cv_mean']:.3f} ± {result['cv_std']:.3f}")
            report.append("")
        
        report.append(f"BEST MODEL: {self.best_model_name}")
        report.append(f"BEST SCORE: {self.best_score:.3f}")
        
        return "\n".join(report)

def main():
    """Demonstrate complete ML pipeline."""
    print("COMPLETE MACHINE LEARNING PIPELINE")
    print("="*50)
    
    # Initialize pipeline
    pipeline = MLPipeline()
    
    # Create classification dataset
    print("1. Creating classification dataset...")
    pipeline.create_classification_dataset(n_samples=1000, n_features=15, n_classes=3)
    
    # Preprocess data
    print("\n2. Preprocessing data...")
    pipeline.preprocess_data(test_size=0.2, scaler_type='standard')
    
    # Feature selection
    print("\n3. Performing feature selection...")
    pipeline.feature_selection(method='selectkbest', k=8)
    
    # Train models
    print("\n4. Training classification models...")
    pipeline.train_classification_models()
    
    # Evaluate best model
    print("\n5. Evaluating best model...")
    pipeline.evaluate_model()
    
    # Hyperparameter tuning
    print("\n6. Performing hyperparameter tuning...")
    pipeline.hyperparameter_tuning()
    
    # Clustering analysis
    print("\n7. Performing clustering analysis...")
    pipeline.perform_clustering(n_clusters=3)
    
    # Dimensionality reduction
    print("\n8. Performing dimensionality reduction...")
    pipeline.dimensionality_reduction(method='pca', n_components=2)
    
    # Generate report
    print("\n9. Generating ML report...")
    report = pipeline.generate_report()
    print(report)
    
    # Save report
    with open('ml_pipeline_report.txt', 'w') as f:
        f.write(report)
    
    print("\nML Pipeline complete! Report saved to 'ml_pipeline_report.txt'")

if __name__ == "__main__":
    main()
