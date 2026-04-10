"""
Data Analysis with Pandas
Comprehensive data analysis and visualization using pandas and matplotlib.
"""

import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LinearRegression, LogisticRegression
from sklearn.metrics import mean_squared_error, accuracy_score, classification_report
from sklearn.cluster import KMeans
from sklearn.decomposition import PCA
import warnings
warnings.filterwarnings('ignore')

class DataAnalyzer:
    """Comprehensive data analysis toolkit."""
    
    def __init__(self):
        """Initialize the analyzer."""
        self.data = None
        self.scaler = StandardScaler()
        self.label_encoder = LabelEncoder()
    
    def load_data(self, source, file_type='csv'):
        """
        Load data from various sources.
        
        Args:
            source: File path or URL
            file_type: Type of file ('csv', 'excel', 'json')
        """
        try:
            if file_type == 'csv':
                self.data = pd.read_csv(source)
            elif file_type == 'excel':
                self.data = pd.read_excel(source)
            elif file_type == 'json':
                self.data = pd.read_json(source)
            else:
                raise ValueError(f"Unsupported file type: {file_type}")
            
            print(f"Data loaded successfully! Shape: {self.data.shape}")
            return True
        
        except Exception as e:
            print(f"Error loading data: {e}")
            return False
    
    def create_sample_data(self, data_type='sales'):
        """Create sample datasets for demonstration."""
        if data_type == 'sales':
            # Sales data
            np.random.seed(42)
            dates = pd.date_range('2023-01-01', '2023-12-31', freq='D')
            products = ['Laptop', 'Phone', 'Tablet', 'Watch', 'Headphones']
            regions = ['North', 'South', 'East', 'West']
            
            data = {
                'date': np.random.choice(dates, 1000),
                'product': np.random.choice(products, 1000),
                'region': np.random.choice(regions, 1000),
                'quantity': np.random.randint(1, 10, 1000),
                'price': np.random.uniform(50, 1000, 1000),
                'customer_age': np.random.randint(18, 70, 1000),
                'customer_gender': np.random.choice(['M', 'F'], 1000)
            }
            
            self.data = pd.DataFrame(data)
            self.data['total_sale'] = self.data['quantity'] * self.data['price']
            
        elif data_type == 'iris':
            # Classic iris dataset
            from sklearn.datasets import load_iris
            iris = load_iris()
            self.data = pd.DataFrame(
                iris.data,
                columns=iris.feature_names
            )
            self.data['species'] = iris.target
            
        elif data_type == 'titanic':
            # Titanic-like dataset
            np.random.seed(42)
            n_passengers = 891
            
            data = {
                'passenger_id': range(1, n_passengers + 1),
                'survived': np.random.choice([0, 1], n_passengers, p=[0.62, 0.38]),
                'pclass': np.random.choice([1, 2, 3], n_passengers, p=[0.25, 0.25, 0.5]),
                'sex': np.random.choice(['male', 'female'], n_passengers),
                'age': np.random.normal(30, 15, n_passengers),
                'sibsp': np.random.randint(0, 5, n_passengers),
                'parch': np.random.randint(0, 5, n_passengers),
                'fare': np.random.exponential(30, n_passengers)
            }
            
            self.data = pd.DataFrame(data)
            self.data['age'] = np.clip(self.data['age'], 0, 80)
            self.data['fare'] = np.clip(self.data['fare'], 5, 500)
        
        print(f"Sample {data_type} data created! Shape: {self.data.shape}")
    
    def explore_data(self):
        """Perform exploratory data analysis."""
        if self.data is None:
            print("No data loaded!")
            return
        
        print("\n" + "="*50)
        print("EXPLORATORY DATA ANALYSIS")
        print("="*50)
        
        # Basic info
        print(f"\nDataset Shape: {self.data.shape}")
        print(f"Memory Usage: {self.data.memory_usage(deep=True).sum() / 1024**2:.2f} MB")
        
        # Data types
        print("\nData Types:")
        print(self.data.dtypes)
        
        # Missing values
        print("\nMissing Values:")
        missing = self.data.isnull().sum()
        print(missing[missing > 0] if missing.any() else "No missing values")
        
        # Basic statistics
        print("\nNumerical Columns Statistics:")
        print(self.data.describe())
        
        # Categorical columns
        categorical_cols = self.data.select_dtypes(include=['object']).columns
        if len(categorical_cols) > 0:
            print("\nCategorical Columns Summary:")
            for col in categorical_cols:
                print(f"\n{col}:")
                print(self.data[col].value_counts())
    
    def clean_data(self):
        """Clean and preprocess data."""
        if self.data is None:
            print("No data loaded!")
            return
        
        print("\n" + "="*30)
        print("DATA CLEANING")
        print("="*30)
        
        # Handle missing values
        missing_before = self.data.isnull().sum().sum()
        
        # Numerical columns - fill with median
        numeric_cols = self.data.select_dtypes(include=[np.number]).columns
        for col in numeric_cols:
            if self.data[col].isnull().any():
                median_val = self.data[col].median()
                self.data[col].fillna(median_val, inplace=True)
                print(f"Filled missing values in {col} with median: {median_val}")
        
        # Categorical columns - fill with mode
        categorical_cols = self.data.select_dtypes(include=['object']).columns
        for col in categorical_cols:
            if self.data[col].isnull().any():
                mode_val = self.data[col].mode()[0]
                self.data[col].fillna(mode_val, inplace=True)
                print(f"Filled missing values in {col} with mode: {mode_val}")
        
        missing_after = self.data.isnull().sum().sum()
        print(f"\nMissing values: {missing_before} -> {missing_after}")
        
        # Remove duplicates
        duplicates_before = self.data.duplicated().sum()
        self.data.drop_duplicates(inplace=True)
        duplicates_after = self.data.duplicated().sum()
        print(f"Duplicates: {duplicates_before} -> {duplicates_after}")
        
        # Data type optimization
        for col in self.data.columns:
            if self.data[col].dtype == 'int64':
                if self.data[col].min() >= 0:
                    if self.data[col].max() < 255:
                        self.data[col] = self.data[col].astype('uint8')
                    elif self.data[col].max() < 65535:
                        self.data[col] = self.data[col].astype('uint16')
        
        print(f"Final shape: {self.data.shape}")
    
    def visualize_data(self, plot_type='all'):
        """Create various visualizations."""
        if self.data is None:
            print("No data loaded!")
            return
        
        plt.style.use('seaborn-v0_8')
        
        if plot_type in ['all', 'distribution']:
            # Distribution plots
            numeric_cols = self.data.select_dtypes(include=[np.number]).columns
            if len(numeric_cols) > 0:
                fig, axes = plt.subplots(2, 2, figsize=(15, 10))
                axes = axes.flatten()
                
                for i, col in enumerate(numeric_cols[:4]):
                    axes[i].hist(self.data[col], bins=30, alpha=0.7)
                    axes[i].set_title(f'Distribution of {col}')
                    axes[i].set_xlabel(col)
                    axes[i].set_ylabel('Frequency')
                
                plt.tight_layout()
                plt.show()
        
        if plot_type in ['all', 'correlation']:
            # Correlation heatmap
            numeric_cols = self.data.select_dtypes(include=[np.number]).columns
            if len(numeric_cols) > 1:
                plt.figure(figsize=(12, 8))
                correlation_matrix = self.data[numeric_cols].corr()
                sns.heatmap(correlation_matrix, annot=True, cmap='coolwarm', center=0)
                plt.title('Correlation Matrix')
                plt.show()
        
        if plot_type in ['all', 'categorical']:
            # Categorical plots
            categorical_cols = self.data.select_dtypes(include=['object']).columns
            if len(categorical_cols) > 0:
                fig, axes = plt.subplots(2, 2, figsize=(15, 10))
                axes = axes.flatten()
                
                for i, col in enumerate(categorical_cols[:4]):
                    value_counts = self.data[col].value_counts()
                    axes[i].bar(value_counts.index, value_counts.values)
                    axes[i].set_title(f'Count of {col}')
                    axes[i].tick_params(axis='x', rotation=45)
                
                plt.tight_layout()
                plt.show()
    
    def perform_regression(self, target_col):
        """Perform linear regression analysis."""
        if self.data is None:
            print("No data loaded!")
            return
        
        # Prepare data
        numeric_cols = self.data.select_dtypes(include=[np.number]).columns
        if target_col not in numeric_cols:
            print(f"Target column {target_col} must be numeric!")
            return
        
        X = self.data[numeric_cols.drop(target_col)]
        y = self.data[target_col]
        
        # Handle categorical variables
        categorical_cols = X.select_dtypes(include=['object']).columns
        if len(categorical_cols) > 0:
            X = pd.get_dummies(X, columns=categorical_cols, drop_first=True)
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Train model
        model = LinearRegression()
        model.fit(X_train_scaled, y_train)
        
        # Make predictions
        y_pred = model.predict(X_test_scaled)
        
        # Evaluate
        mse = mean_squared_error(y_test, y_pred)
        rmse = np.sqrt(mse)
        
        print("\n" + "="*40)
        print("LINEAR REGRESSION RESULTS")
        print("="*40)
        print(f"Target: {target_col}")
        print(f"Features: {X.shape[1]}")
        print(f"RMSE: {rmse:.2f}")
        print(f"R² Score: {model.score(X_test_scaled, y_test):.3f}")
        
        # Feature importance
        if hasattr(model, 'coef_'):
            feature_importance = pd.DataFrame({
                'feature': X.columns,
                'importance': np.abs(model.coef_)
            }).sort_values('importance', ascending=False)
            
            print("\nTop 10 Important Features:")
            print(feature_importance.head(10))
        
        return model, X_train_scaled, X_test_scaled, y_train, y_test
    
    def perform_classification(self, target_col):
        """Perform classification analysis."""
        if self.data is None:
            print("No data loaded!")
            return
        
        # Prepare data
        X = self.data.drop(columns=[target_col])
        y = self.data[target_col]
        
        # Handle categorical variables
        categorical_cols = X.select_dtypes(include=['object']).columns
        if len(categorical_cols) > 0:
            X = pd.get_dummies(X, columns=categorical_cols, drop_first=True)
        
        # Encode target if it's categorical
        if y.dtype == 'object':
            y = self.label_encoder.fit_transform(y)
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Train model
        model = LogisticRegression(random_state=42)
        model.fit(X_train_scaled, y_train)
        
        # Make predictions
        y_pred = model.predict(X_test_scaled)
        
        # Evaluate
        accuracy = accuracy_score(y_test, y_pred)
        
        print("\n" + "="*40)
        print("CLASSIFICATION RESULTS")
        print("="*40)
        print(f"Target: {target_col}")
        print(f"Features: {X.shape[1]}")
        print(f"Accuracy: {accuracy:.3f}")
        print("\nClassification Report:")
        print(classification_report(y_test, y_pred))
        
        return model, X_train_scaled, X_test_scaled, y_train, y_test
    
    def perform_clustering(self, n_clusters=3):
        """Perform K-means clustering."""
        if self.data is None:
            print("No data loaded!")
            return
        
        # Prepare data
        numeric_cols = self.data.select_dtypes(include=[np.number]).columns
        X = self.data[numeric_cols]
        
        # Handle missing values
        X = X.fillna(X.median())
        
        # Scale features
        X_scaled = self.scaler.fit_transform(X)
        
        # Perform clustering
        kmeans = KMeans(n_clusters=n_clusters, random_state=42)
        cluster_labels = kmeans.fit_predict(X_scaled)
        
        # Add cluster labels to data
        self.data['cluster'] = cluster_labels
        
        # Evaluate clusters
        print("\n" + "="*30)
        print("CLUSTERING RESULTS")
        print("="*30)
        print(f"Number of clusters: {n_clusters}")
        print(f"Inertia: {kmeans.inertia_:.2f}")
        
        # Cluster sizes
        cluster_sizes = pd.Series(cluster_labels).value_counts().sort_index()
        print("\nCluster Sizes:")
        for cluster, size in cluster_sizes.items():
            print(f"Cluster {cluster}: {size} samples")
        
        # Cluster characteristics
        print("\nCluster Characteristics:")
        for cluster in range(n_clusters):
            cluster_data = self.data[self.data['cluster'] == cluster][numeric_cols]
            print(f"\nCluster {cluster}:")
            print(cluster_data.describe().iloc[[1, 5, 6]])  # mean, min, max
        
        # Visualize clusters (if 2D or 3D)
        if X.shape[1] >= 2:
            plt.figure(figsize=(10, 8))
            
            # Use PCA for visualization if more than 2 dimensions
            if X.shape[1] > 2:
                pca = PCA(n_components=2)
                X_pca = pca.fit_transform(X_scaled)
                plt.scatter(X_pca[:, 0], X_pca[:, 1], c=cluster_labels, cmap='viridis', alpha=0.7)
                plt.xlabel(f'PC1 ({pca.explained_variance_ratio_[0]:.2%} variance)')
                plt.ylabel(f'PC2 ({pca.explained_variance_ratio_[1]:.2%} variance)')
            else:
                plt.scatter(X_scaled[:, 0], X_scaled[:, 1], c=cluster_labels, cmap='viridis', alpha=0.7)
                plt.xlabel(numeric_cols[0])
                plt.ylabel(numeric_cols[1])
            
            plt.title('K-Means Clustering Results')
            plt.colorbar()
            plt.show()
        
        return kmeans, X_scaled, cluster_labels
    
    def generate_report(self):
        """Generate a comprehensive analysis report."""
        if self.data is None:
            print("No data loaded!")
            return
        
        report = []
        report.append("DATA ANALYSIS REPORT")
        report.append("="*50)
        report.append(f"Dataset Shape: {self.data.shape}")
        report.append(f"Memory Usage: {self.data.memory_usage(deep=True).sum() / 1024**2:.2f} MB")
        report.append("")
        
        # Data types
        report.append("DATA TYPES:")
        for col, dtype in self.data.dtypes.items():
            report.append(f"  {col}: {dtype}")
        report.append("")
        
        # Missing values
        missing = self.data.isnull().sum()
        if missing.any():
            report.append("MISSING VALUES:")
            for col, count in missing[missing > 0].items():
                report.append(f"  {col}: {count}")
        else:
            report.append("MISSING VALUES: None")
        report.append("")
        
        # Statistics
        report.append("NUMERICAL STATISTICS:")
        numeric_cols = self.data.select_dtypes(include=[np.number]).columns
        if len(numeric_cols) > 0:
            stats = self.data[numeric_cols].describe()
            for col in numeric_cols:
                report.append(f"  {col}:")
                report.append(f"    Mean: {stats.loc['mean', col]:.2f}")
                report.append(f"    Std:  {stats.loc['std', col]:.2f}")
                report.append(f"    Min:  {stats.loc['min', col]:.2f}")
                report.append(f"    Max:  {stats.loc['max', col]:.2f}")
        
        # Categorical summary
        categorical_cols = self.data.select_dtypes(include=['object']).columns
        if len(categorical_cols) > 0:
            report.append("")
            report.append("CATEGORICAL SUMMARY:")
            for col in categorical_cols:
                value_counts = self.data[col].value_counts()
                report.append(f"  {col}: {len(value_counts)} unique values")
                report.append(f"    Most common: {value_counts.index[0]} ({value_counts.iloc[0]} occurrences)")
        
        return "\n".join(report)

def main():
    """Demonstrate comprehensive data analysis."""
    print("COMPREHENSIVE DATA ANALYSIS")
    print("="*40)
    
    analyzer = DataAnalyzer()
    
    # Create sample sales data
    print("1. Creating sample sales data...")
    analyzer.create_sample_data('sales')
    
    # Exploratory data analysis
    print("\n2. Performing exploratory data analysis...")
    analyzer.explore_data()
    
    # Data cleaning
    print("\n3. Cleaning data...")
    analyzer.clean_data()
    
    # Visualization
    print("\n4. Creating visualizations...")
    analyzer.visualize_data()
    
    # Regression analysis
    print("\n5. Performing regression analysis...")
    analyzer.perform_regression('total_sale')
    
    # Clustering
    print("\n6. Performing clustering analysis...")
    analyzer.perform_clustering(n_clusters=4)
    
    # Generate report
    print("\n7. Generating analysis report...")
    report = analyzer.generate_report()
    print(report)
    
    # Save report to file
    with open('data_analysis_report.txt', 'w') as f:
        f.write(report)
    
    print("\nAnalysis complete! Report saved to 'data_analysis_report.txt'")

if __name__ == "__main__":
    main()
