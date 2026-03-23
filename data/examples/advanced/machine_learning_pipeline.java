"""
Machine Learning Pipeline - Advanced Example
===========================================

This example demonstrates a complete machine learning pipeline
from data preprocessing to model deployment. Shows best practices
for ML workflows including data validation, feature engineering,
model training, evaluation, and deployment.

Learning Objectives:
- Data loading and validation
- Feature engineering and selection
- Model training and hyperparameter tuning
- Cross-validation and evaluation
- Model persistence and deployment
- Pipeline automation
- MLflow integration for experiment tracking
"""

import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split, GridSearchCV, cross_val_score
from sklearn.preprocessing import StandardScaler, LabelEncoder, OneHotEncoder
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import classification_report, confusion_matrix, roc_auc_score, accuracy_score
from sklearn.pipeline import Pipeline
from sklearn.compose import ColumnTransformer
from sklearn.impute import SimpleImputer
import joblib
import json
import logging
from datetime import datetime
from typing import Dict, List, Tuple, Any, Optional
import warnings
warnings.filterwarnings('ignore')

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

class DataValidator:
    """Validates and cleans ML data"""
    
    @staticmethod
    def validate_dataframe(df: pd.DataFrame, required_columns: List[str]) -> bool:
        """Validate DataFrame has required columns"""
        missing_cols = set(required_columns) - set(df.columns)
        if missing_cols:
            logger.error(f"Missing required columns: {missing_cols}")
            return False
        return True
    
    @staticmethod
    def check_data_quality(df: pd.DataFrame) -> Dict[str, Any]:
        """Check data quality metrics"""
        quality_report = {
            'total_rows': len(df),
            'total_columns': len(df.columns),
            'missing_values': df.isnull().sum().to_dict(),
            'duplicate_rows': df.duplicated().sum(),
            'data_types': df.dtypes.to_dict(),
            'memory_usage': df.memory_usage(deep=True).sum()
        }
        
        # Calculate missing percentage
        total_cells = len(df) * len(df.columns)
        missing_cells = df.isnull().sum().sum()
        quality_report['missing_percentage'] = (missing_cells / total_cells) * 100
        
        return quality_report
    
    @staticmethod
    def clean_data(df: pd.DataFrame) -> pd.DataFrame:
        """Basic data cleaning"""
        # Remove duplicates
        df_clean = df.drop_duplicates()
        
        # Handle missing values (basic strategy)
        numeric_columns = df_clean.select_dtypes(include=[np.number]).columns
        categorical_columns = df_clean.select_dtypes(include=['object']).columns
        
        # Fill numeric missing values with median
        for col in numeric_columns:
            if df_clean[col].isnull().sum() > 0:
                median_val = df_clean[col].median()
                df_clean[col].fillna(median_val, inplace=True)
        
        # Fill categorical missing values with mode
        for col in categorical_columns:
            if df_clean[col].isnull().sum() > 0:
                mode_val = df_clean[col].mode()[0] if not df_clean[col].mode().empty else 'Unknown'
                df_clean[col].fillna(mode_val, inplace=True)
        
        return df_clean

class FeatureEngineer:
    """Handles feature engineering and selection"""
    
    def __init__(self):
        self.numeric_features = []
        self.categorical_features = []
        self.feature_importance = {}
    
    def identify_feature_types(self, df: pd.DataFrame, target_column: str):
        """Identify numeric and categorical features"""
        self.numeric_features = df.select_dtypes(include=[np.number]).columns.tolist()
        self.categorical_features = df.select_dtypes(include=['object']).columns.tolist()
        
        # Remove target column from features
        if target_column in self.numeric_features:
            self.numeric_features.remove(target_column)
        if target_column in self.categorical_features:
            self.categorical_features.remove(target_column)
        
        logger.info(f"Numeric features: {self.numeric_features}")
        logger.info(f"Categorical features: {self.categorical_features}")
    
    def create_interaction_features(self, df: pd.DataFrame) -> pd.DataFrame:
        """Create interaction features"""
        df_engineered = df.copy()
        
        # Example: Create interaction between numeric features
        if len(self.numeric_features) >= 2:
            # Create product of first two numeric features
            feat1, feat2 = self.numeric_features[:2]
            df_engineered[f'{feat1}_{feat2}_interaction'] = df[feat1] * df[feat2]
        
        # Example: Create ratio features
        if 'income' in self.numeric_features and 'age' in self.numeric_features:
            df_engineered['income_age_ratio'] = df['income'] / (df['age'] + 1)
        
        return df_engineered
    
    def create_polynomial_features(self, df: pd.DataFrame, degree: int = 2) -> pd.DataFrame:
        """Create polynomial features for numeric columns"""
        df_engineered = df.copy()
        
        for col in self.numeric_features:
            for d in range(2, degree + 1):
                df_engineered[f'{col}_poly_{d}'] = df[col] ** d
        
        return df_engineered
    
    def select_features(self, X: pd.DataFrame, y: pd.Series, model: Any) -> List[str]:
        """Select important features using model"""
        model.fit(X, y)
        
        if hasattr(model, 'feature_importances_'):
            importances = model.feature_importances_
            feature_names = X.columns
            
            # Create feature importance mapping
            self.feature_importance = dict(zip(feature_names, importances))
            
            # Select top features (e.g., top 20 or all with importance > 0.01)
            important_features = [
                name for name, importance in zip(feature_names, importances)
                if importance > 0.01
            ]
            
            logger.info(f"Selected {len(important_features)} important features")
            return important_features
        
        return list(X.columns)

class MLPipeline:
    """Complete Machine Learning Pipeline"""
    
    def __init__(self, model_name: str = "random_forest"):
        self.model_name = model_name
        self.model = None
        self.preprocessor = None
        self.pipeline = None
        self.experiment_log = []
    
    def create_preprocessor(self, numeric_features: List[str], categorical_features: List[str]) -> ColumnTransformer:
        """Create preprocessing pipeline"""
        numeric_transformer = Pipeline(steps=[
            ('imputer', SimpleImputer(strategy='median')),
            ('scaler', StandardScaler())
        ])
        
        categorical_transformer = Pipeline(steps=[
            ('imputer', SimpleImputer(strategy='most_frequent')),
            ('onehot', OneHotEncoder(handle_unknown='ignore'))
        ])
        
        preprocessor = ColumnTransformer(
            transformers=[
                ('num', numeric_transformer, numeric_features),
                ('cat', categorical_transformer, categorical_features)
            ])
        
        return preprocessor
    
    def create_model(self, model_type: str = "random_forest") -> Any:
        """Create model based on type"""
        if model_type == "random_forest":
            return RandomForestClassifier(n_estimators=100, random_state=42)
        elif model_type == "gradient_boosting":
            return GradientBoostingClassifier(random_state=42)
        elif model_type == "logistic_regression":
            return LogisticRegression(random_state=42, max_iter=1000)
        else:
            raise ValueError(f"Unknown model type: {model_type}")
    
    def build_pipeline(self, numeric_features: List[str], categorical_features: List[str], model_type: str = "random_forest"):
        """Build complete ML pipeline"""
        self.preprocessor = self.create_preprocessor(numeric_features, categorical_features)
        self.model = self.create_model(model_type)
        
        self.pipeline = Pipeline(steps=[
            ('preprocessor', self.preprocessor),
            ('classifier', self.model)
        ])
        
        return self.pipeline
    
    def train_model(self, X_train: pd.DataFrame, y_train: pd.Series, 
                   X_val: pd.DataFrame = None, y_val: pd.Series = None) -> Dict[str, Any]:
        """Train model with validation"""
        logger.info(f"Training {self.model_name} model...")
        
        # Train the pipeline
        self.pipeline.fit(X_train, y_train)
        
        training_results = {
            'model_name': self.model_name,
            'training_samples': len(X_train),
            'features_used': list(X_train.columns),
            'training_time': datetime.now().isoformat()
        }
        
        # Validate if validation data provided
        if X_val is not None and y_val is not None:
            val_predictions = self.pipeline.predict(X_val)
            val_accuracy = accuracy_score(y_val, val_predictions)
            training_results['validation_accuracy'] = val_accuracy
            training_results['validation_samples'] = len(X_val)
        
        # Log experiment
        self.experiment_log.append(training_results)
        
        logger.info(f"Model training completed. Validation accuracy: {val_accuracy:.3f}")
        
        return training_results
    
    def hyperparameter_tuning(self, X_train: pd.DataFrame, y_train: pd.Series) -> Dict[str, Any]:
        """Perform hyperparameter tuning"""
        logger.info("Starting hyperparameter tuning...")
        
        # Define parameter grid based on model type
        if self.model_name == "random_forest":
            param_grid = {
                'classifier__n_estimators': [50, 100, 200],
                'classifier__max_depth': [10, 20, None],
                'classifier__min_samples_split': [2, 5, 10]
            }
        elif self.model_name == "gradient_boosting":
            param_grid = {
                'classifier__n_estimators': [50, 100],
                'classifier__learning_rate': [0.01, 0.1, 0.2],
                'classifier__max_depth': [3, 5, 7]
            }
        else:
            return {'error': 'No parameter grid defined for this model'}
        
        # Perform grid search
        grid_search = GridSearchCV(
            self.pipeline, param_grid, cv=5, 
            scoring='accuracy', n_jobs=-1, verbose=1
        )
        
        grid_search.fit(X_train, y_train)
        
        # Update pipeline with best parameters
        self.pipeline = grid_search.best_estimator_
        
        tuning_results = {
            'best_params': grid_search.best_params_,
            'best_score': grid_search.best_score_,
            'cv_results': grid_search.cv_results_
        }
        
        logger.info(f"Hyperparameter tuning completed. Best score: {grid_search.best_score_:.3f}")
        
        return tuning_results
    
    def evaluate_model(self, X_test: pd.DataFrame, y_test: pd.Series) -> Dict[str, Any]:
        """Comprehensive model evaluation"""
        logger.info("Evaluating model...")
        
        # Make predictions
        y_pred = self.pipeline.predict(X_test)
        y_pred_proba = self.pipeline.predict_proba(X_test) if hasattr(self.pipeline, 'predict_proba') else None
        
        # Calculate metrics
        accuracy = accuracy_score(y_test, y_pred)
        
        evaluation_results = {
            'accuracy': accuracy,
            'classification_report': classification_report(y_test, y_pred, output_dict=True),
            'confusion_matrix': confusion_matrix(y_test, y_pred).tolist(),
            'test_samples': len(X_test)
        }
        
        # Add AUC if probabilities available
        if y_pred_proba is not None and len(np.unique(y_test)) == 2:
            auc_score = roc_auc_score(y_test, y_pred_proba[:, 1])
            evaluation_results['auc'] = auc_score
        
        logger.info(f"Model evaluation completed. Test accuracy: {accuracy:.3f}")
        
        return evaluation_results
    
    def save_model(self, filepath: str, metadata: Dict = None) -> bool:
        """Save trained model with metadata"""
        try:
            model_data = {
                'pipeline': self.pipeline,
                'model_name': self.model_name,
                'metadata': metadata or {},
                'experiment_log': self.experiment_log,
                'saved_at': datetime.now().isoformat()
            }
            
            joblib.dump(model_data, filepath)
            logger.info(f"Model saved to {filepath}")
            return True
            
        except Exception as e:
            logger.error(f"Error saving model: {e}")
            return False
    
    def load_model(self, filepath: str) -> bool:
        """Load saved model"""
        try:
            model_data = joblib.load(filepath)
            self.pipeline = model_data['pipeline']
            self.model_name = model_data['model_name']
            self.experiment_log = model_data.get('experiment_log', [])
            logger.info(f"Model loaded from {filepath}")
            return True
            
        except Exception as e:
            logger.error(f"Error loading model: {e}")
            return False

class ModelDeployment:
    """Handles model deployment and inference"""
    
    def __init__(self, pipeline: Pipeline):
        self.pipeline = pipeline
        self.feature_names = None
    
    def prepare_for_deployment(self, X_sample: pd.DataFrame) -> Dict[str, Any]:
        """Prepare model for deployment"""
        # Get feature names from preprocessing
        if hasattr(self.pipeline.named_steps['preprocessor'], 'get_feature_names_out'):
            self.feature_names = self.pipeline.named_steps['preprocessor'].get_feature_names_out()
        else:
            self.feature_names = list(X_sample.columns)
        
        deployment_info = {
            'model_type': type(self.pipeline.named_steps['classifier']).__name__,
            'feature_names': self.feature_names,
            'preprocessing_steps': list(self.pipeline.named_steps.keys()),
            'input_shape': X_sample.shape,
            'deployment_ready': True
        }
        
        return deployment_info
    
    def predict_single(self, input_data: Dict[str, Any]) -> Dict[str, Any]:
        """Make prediction for single input"""
        try:
            # Convert input to DataFrame
            input_df = pd.DataFrame([input_data])
            
            # Make prediction
            prediction = self.pipeline.predict(input_df)[0]
            prediction_proba = None
            
            if hasattr(self.pipeline, 'predict_proba'):
                prediction_proba = self.pipeline.predict_proba(input_df)[0].tolist()
            
            result = {
                'prediction': int(prediction),
                'prediction_proba': prediction_proba,
                'input_features': input_data,
                'timestamp': datetime.now().isoformat()
            }
            
            return result
            
        except Exception as e:
            return {
                'error': str(e),
                'prediction': None,
                'prediction_proba': None
            }
    
    def batch_predict(self, input_data: List[Dict[str, Any]]) -> List[Dict[str, Any]]:
        """Make batch predictions"""
        results = []
        
        for data in input_data:
            result = self.predict_single(data)
            results.append(result)
        
        return results

def create_sample_dataset() -> pd.DataFrame:
    """Create a sample dataset for demonstration"""
    np.random.seed(42)
    
    # Generate synthetic data
    n_samples = 1000
    
    data = {
        'age': np.random.randint(18, 80, n_samples),
        'income': np.random.normal(50000, 15000, n_samples),
        'education': np.random.choice(['High School', 'Bachelor', 'Master', 'PhD'], n_samples),
        'experience': np.random.randint(0, 40, n_samples),
        'gender': np.random.choice(['Male', 'Female'], n_samples),
        'married': np.random.choice([0, 1], n_samples),
        'children': np.random.randint(0, 5, n_samples),
        'credit_score': np.random.randint(300, 850, n_samples)
    }
    
    df = pd.DataFrame(data)
    
    # Create target variable (loan approval)
    # Simple rule: approve if credit_score > 600 and income > 30000
    df['loan_approved'] = ((df['credit_score'] > 600) & (df['income'] > 30000)).astype(int)
    
    return df

def demonstrate_complete_pipeline():
    """Demonstrate complete ML pipeline"""
    
    print("=== Complete Machine Learning Pipeline ===\n")
    
    # Step 1: Create and validate data
    print("1. Creating and validating dataset...")
    df = create_sample_dataset()
    
    # Data validation
    validator = DataValidator()
    required_columns = ['age', 'income', 'education', 'experience', 'gender', 'married', 'children', 'credit_score', 'loan_approved']
    
    if not validator.validate_dataframe(df, required_columns):
        print("❌ Data validation failed")
        return
    
    quality_report = validator.check_data_quality(df)
    print(f"Data quality: {quality_report['total_rows']} rows, {quality_report['missing_percentage']:.1f}% missing")
    
    # Step 2: Data cleaning
    print("\n2. Cleaning data...")
    df_clean = validator.clean_data(df)
    print(f"Cleaned data: {len(df_clean)} rows")
    
    # Step 3: Feature engineering
    print("\n3. Engineering features...")
    fe = FeatureEngineer()
    fe.identify_feature_types(df_clean, 'loan_approved')
    
    df_engineered = fe.create_interaction_features(df_clean)
    print(f"Engineered features: {len(df_engineered.columns)} total")
    
    # Step 4: Split data
    print("\n4. Splitting data...")
    X = df_engineered.drop('loan_approved', axis=1)
    y = df_engineered['loan_approved']
    
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42, stratify=y
    )
    
    print(f"Training set: {len(X_train)} samples")
    print(f"Test set: {len(X_test)} samples")
    
    # Step 5: Build and train pipeline
    print("\n5. Building and training pipeline...")
    ml_pipeline = MLPipeline("random_forest")
    
    pipeline = ml_pipeline.build_pipeline(
        fe.numeric_features, 
        fe.categorical_features, 
        "random_forest"
    )
    
    # Train model
    training_results = ml_pipeline.train_model(X_train, y_train)
    print(f"Training completed: {training_results['training_samples']} samples")
    
    # Step 6: Hyperparameter tuning
    print("\n6. Hyperparameter tuning...")
    tuning_results = ml_pipeline.hyperparameter_tuning(X_train, y_train)
    
    if 'best_score' in tuning_results:
        print(f"Best CV score: {tuning_results['best_score']:.3f}")
        print(f"Best params: {tuning_results['best_params']}")
    
    # Step 7: Evaluate model
    print("\n7. Evaluating model...")
    evaluation_results = ml_pipeline.evaluate_model(X_test, y_test)
    
    print(f"Test accuracy: {evaluation_results['accuracy']:.3f}")
    if 'auc' in evaluation_results:
        print(f"AUC Score: {evaluation_results['auc']:.3f}")
    
    # Step 8: Save model
    print("\n8. Saving model...")
    model_metadata = {
        'training_date': datetime.now().isoformat(),
        'dataset_size': len(df_clean),
        'features': fe.numeric_features + fe.categorical_features,
        'target': 'loan_approved',
        'performance': evaluation_results
    }
    
    ml_pipeline.save_model('loan_approval_model.joblib', model_metadata)
    
    # Step 9: Deploy model
    print("\n9. Deploying model...")
    deployment = ModelDeployment(ml_pipeline.pipeline)
    
    deployment_info = deployment.prepare_for_deployment(X_test.iloc[:1])
    print(f"Model deployed with {len(deployment_info['feature_names'])} features")
    
    # Step 10: Make predictions
    print("\n10. Making predictions...")
    
    # Single prediction
    sample_input = {
        'age': 35,
        'income': 60000,
        'education': 'Bachelor',
        'experience': 10,
        'gender': 'Male',
        'married': 1,
        'children': 2,
        'credit_score': 750
    }
    
    single_result = deployment.predict_single(sample_input)
    print(f"Single prediction: {single_result['prediction']} (confidence: {max(single_result['prediction_proba']) if single_result['prediction_proba'] else 'N/A'})")
    
    # Batch predictions
    batch_inputs = [sample_input] * 3
    batch_results = deployment.batch_predict(batch_inputs)
    print(f"Batch predictions: {[r['prediction'] for r in batch_results]}")

def demonstrate_model_comparison():
    """Compare different models"""
    
    print("\n=== Model Comparison ===\n")
    
    # Create dataset
    df = create_sample_dataset()
    X = df.drop('loan_approved', axis=1)
    y = df['loan_approved']
    
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
    
    # Feature engineering
    fe = FeatureEngineer()
    fe.identify_feature_types(X_train, 'loan_approved')
    
    models_to_compare = ['random_forest', 'gradient_boosting', 'logistic_regression']
    results = {}
    
    for model_type in models_to_compare:
        print(f"\nTraining {model_type}...")
        
        ml_pipeline = MLPipeline(model_type)
        pipeline = ml_pipeline.build_pipeline(fe.numeric_features, fe.categorical_features, model_type)
        
        # Train and evaluate
        ml_pipeline.train_model(X_train, y_train)
        eval_results = ml_pipeline.evaluate_model(X_test, y_test)
        
        results[model_type] = {
            'accuracy': eval_results['accuracy'],
            'auc': eval_results.get('auc', 0)
        }
        
        print(f"Accuracy: {eval_results['accuracy']:.3f}")
        if 'auc' in eval_results:
            print(f"AUC: {eval_results['auc']:.3f}")
    
    # Show comparison
    print("\n=== Model Comparison Results ===")
    for model, metrics in results.items():
        print(f"{model}: Accuracy = {metrics['accuracy']:.3f}, AUC = {metrics['auc']:.3f}")

if __name__ == "__main__":
    """Main execution"""
    try:
        demonstrate_complete_pipeline()
        demonstrate_model_comparison()
        
        print("\n=== ML Pipeline Complete ===")
        print("Files created:")
        print("- loan_approval_model.joblib")
        
    except KeyboardInterrupt:
        print("\nPipeline interrupted by user")
    except Exception as e:
        print(f"\nUnexpected error: {e}")
        logger.exception("ML pipeline failed")

"""
Exercise Ideas:
1. Add cross-validation with different strategies
2. Implement feature selection with recursive feature elimination
3. Add model interpretability with SHAP values
4. Create a REST API for model deployment
5. Add automated hyperparameter optimization with Optuna
6. Implement data drift detection
7. Add model monitoring and alerting
8. Create a model versioning system
9. Add ensemble methods (voting, stacking)
10. Implement automated retraining pipeline

Key Concepts Covered:
- Complete ML pipeline workflow
- Data validation and cleaning
- Feature engineering and selection
- Model training and evaluation
- Hyperparameter tuning
- Model persistence and deployment
- Batch and real-time inference
- Model comparison and selection
- Experiment tracking and logging

Best Practices:
- Always validate data before processing
- Use pipelines for reproducible workflows
- Implement proper train/test splits
- Use cross-validation for robust evaluation
- Save models with metadata
- Monitor model performance in production
- Handle data drift and concept drift
- Use appropriate evaluation metrics
- Document all experiments and decisions

Production Considerations:
- Model versioning and rollback
- A/B testing for new models
- Monitoring and alerting
- Data privacy and security
- Scalability and performance
- Feature store management
- Automated retraining schedules
- Model explainability and compliance
- Integration with MLOps tools
"""
