<?php
/**
 * Machine Learning Integration in PHP
 * 
 * Integrating ML models with PHP applications and external ML services.
 */

// ML Model Interface
interface MLModel
{
    public function train(array $data, array $labels): void;
    public function predict(array $input): mixed;
    public function predictBatch(array $inputs): array;
    public function save(string $path): void;
    public function load(string $path): void;
    public function getMetrics(): array;
}

// Simple ML Model Manager
class MLModelManager
{
    private array $models = [];
    private array $modelConfigs = [];
    private string $modelPath = './models/';
    
    public function __construct(string $modelPath = './models/')
    {
        $this->modelPath = $modelPath;
        $this->ensureModelDirectory();
    }
    
    public function registerModel(string $name, MLModel $model, array $config = []): void
    {
        $this->models[$name] = $model;
        $this->modelConfigs[$name] = $config;
        
        echo "Model registered: $name\n";
    }
    
    public function getModel(string $name): ?MLModel
    {
        return $this->models[$name] ?? null;
    }
    
    public function trainModel(string $name, array $data, array $labels): void
    {
        if (!isset($this->models[$name])) {
            throw new Exception("Model not found: $name");
        }
        
        echo "Training model: $name\n";
        $this->models[$name]->train($data, $labels);
        echo "Training completed for: $name\n";
    }
    
    public function predict(string $name, array $input): mixed
    {
        if (!isset($this->models[$name])) {
            throw new Exception("Model not found: $name");
        }
        
        return $this->models[$name]->predict($input);
    }
    
    public function predictBatch(string $name, array $inputs): array
    {
        if (!isset($this->models[$name])) {
            throw new Exception("Model not found: $name");
        }
        
        return $this->models[$name]->predictBatch($inputs);
    }
    
    public function saveModel(string $name): void
    {
        if (!isset($this->models[$name])) {
            throw new Exception("Model not found: $name");
        }
        
        $path = $this->modelPath . $name . '.json';
        $this->models[$name]->save($path);
        
        // Save config
        $configPath = $this->modelPath . $name . '_config.json';
        file_put_contents($configPath, json_encode($this->modelConfigs[$name]));
        
        echo "Model saved: $name\n";
    }
    
    public function loadModel(string $name): void
    {
        $path = $this->modelPath . $name . '.json';
        $configPath = $this->modelPath . $name . '_config.json';
        
        if (!file_exists($path)) {
            throw new Exception("Model file not found: $path");
        }
        
        // Load config
        if (file_exists($configPath)) {
            $this->modelConfigs[$name] = json_decode(file_get_contents($configPath), true);
        }
        
        // Create appropriate model type based on config
        $modelType = $this->modelConfigs[$name]['type'] ?? 'simple';
        
        switch ($modelType) {
            case 'simple':
                $model = new SimpleMLModel();
                break;
            case 'classification':
                $model = new ClassificationMLModel();
                break;
            case 'regression':
                $model = new RegressionMLModel();
                break;
            default:
                $model = new SimpleMLModel();
        }
        
        $model->load($path);
        $this->models[$name] = $model;
        
        echo "Model loaded: $name\n";
    }
    
    public function listModels(): array
    {
        return array_keys($this->models);
    }
    
    public function getModelMetrics(string $name): array
    {
        if (!isset($this->models[$name])) {
            throw new Exception("Model not found: $name");
        }
        
        return $this->models[$name]->getMetrics();
    }
    
    private function ensureModelDirectory(): void
    {
        if (!is_dir($this->modelPath)) {
            mkdir($this->modelPath, 0755, true);
        }
    }
}

// Simple ML Model Implementation
class SimpleMLModel implements MLModel
{
    private array $weights = [];
    private array $bias = [];
    private array $metrics = [];
    private bool $isTrained = false;
    
    public function train(array $data, array $labels): void
    {
        if (empty($data) || empty($labels)) {
            throw new Exception('Training data and labels cannot be empty');
        }
        
        if (count($data) !== count($labels)) {
            throw new Exception('Data and labels must have the same length');
        }
        
        // Simple linear regression implementation
        $numFeatures = count($data[0]);
        $this->weights = array_fill(0, $numFeatures, 0);
        $this->bias = [0];
        
        $learningRate = 0.01;
        $epochs = 1000;
        
        for ($epoch = 0; $epoch < $epochs; $epoch++) {
            $totalError = 0;
            
            for ($i = 0; $i < count($data); $i++) {
                $prediction = $this->predictSingle($data[$i]);
                $error = $prediction - $labels[$i];
                $totalError += $error * $error;
                
                // Update weights
                for ($j = 0; $j < $numFeatures; $j++) {
                    $this->weights[$j] -= $learningRate * $error * $data[$i][$j];
                }
                $this->bias[0] -= $learningRate * $error;
            }
            
            if ($epoch % 100 === 0) {
                echo "Epoch $epoch, Error: " . ($totalError / count($data)) . "\n";
            }
        }
        
        $this->isTrained = true;
        $this->metrics = [
            'epochs' => $epochs,
            'final_error' => $totalError / count($data),
            'features' => $numFeatures,
            'samples' => count($data)
        ];
    }
    
    public function predict(array $input): mixed
    {
        if (!$this->isTrained) {
            throw new Exception('Model not trained yet');
        }
        
        return $this->predictSingle($input);
    }
    
    public function predictBatch(array $inputs): array
    {
        if (!$this->isTrained) {
            throw new Exception('Model not trained yet');
        }
        
        $predictions = [];
        foreach ($inputs as $input) {
            $predictions[] = $this->predictSingle($input);
        }
        
        return $predictions;
    }
    
    private function predictSingle(array $input): float
    {
        $prediction = $this->bias[0];
        
        for ($i = 0; $i < count($input); $i++) {
            $prediction += $this->weights[$i] * $input[$i];
        }
        
        return $prediction;
    }
    
    public function save(string $path): void
    {
        $data = [
            'weights' => $this->weights,
            'bias' => $this->bias,
            'metrics' => $this->metrics,
            'is_trained' => $this->isTrained
        ];
        
        file_put_contents($path, json_encode($data));
    }
    
    public function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new Exception("Model file not found: $path");
        }
        
        $data = json_decode(file_get_contents($path), true);
        
        $this->weights = $data['weights'] ?? [];
        $this->bias = $data['bias'] ?? [];
        $this->metrics = $data['metrics'] ?? [];
        $this->isTrained = $data['is_trained'] ?? false;
    }
    
    public function getMetrics(): array
    {
        return $this->metrics;
    }
}

// Classification ML Model
class ClassificationMLModel implements MLModel
{
    private array $classes = [];
    private array $classProbabilities = [];
    private array $featureProbabilities = [];
    private array $metrics = [];
    private bool $isTrained = false;
    
    public function train(array $data, array $labels): void
    {
        if (empty($data) || empty($labels)) {
            throw new Exception('Training data and labels cannot be empty');
        }
        
        if (count($data) !== count($labels)) {
            throw new Exception('Data and labels must have the same length');
        }
        
        // Naive Bayes implementation
        $this->classes = array_unique($labels);
        $totalSamples = count($labels);
        
        // Calculate class probabilities
        $classCounts = array_count_values($labels);
        foreach ($this->classes as $class) {
            $this->classProbabilities[$class] = $classCounts[$class] / $totalSamples;
        }
        
        // Calculate feature probabilities for each class
        foreach ($this->classes as $class) {
            $this->featureProbabilities[$class] = [];
            
            // Get all samples for this class
            $classSamples = [];
            for ($i = 0; $i < count($labels); $i++) {
                if ($labels[$i] === $class) {
                    $classSamples[] = $data[$i];
                }
            }
            
            // Calculate feature statistics
            $numFeatures = count($data[0]);
            for ($feature = 0; $feature < $numFeatures; $feature++) {
                $featureValues = array_column($classSamples, $feature);
                $mean = array_sum($featureValues) / count($featureValues);
                
                $variance = 0;
                foreach ($featureValues as $value) {
                    $variance += pow($value - $mean, 2);
                }
                $stdDev = sqrt($variance / count($featureValues));
                
                $this->featureProbabilities[$class][$feature] = [
                    'mean' => $mean,
                    'std_dev' => $stdDev
                ];
            }
        }
        
        $this->isTrained = true;
        $this->metrics = [
            'classes' => $this->classes,
            'samples' => $totalSamples,
            'features' => count($data[0])
        ];
        
        echo "Classification model trained\n";
    }
    
    public function predict(array $input): mixed
    {
        if (!$this->isTrained) {
            throw new Exception('Model not trained yet');
        }
        
        $probabilities = [];
        
        foreach ($this->classes as $class) {
            $probability = log($this->classProbabilities[$class]);
            
            for ($feature = 0; $feature < count($input); $feature++) {
                $mean = $this->featureProbabilities[$class][$feature]['mean'];
                $stdDev = $this->featureProbabilities[$class][$feature]['std_dev'];
                
                // Gaussian probability
                if ($stdDev > 0) {
                    $exponent = -pow($input[$feature] - $mean, 2) / (2 * $stdDev * $stdDev);
                    $gaussianProb = (1 / ($stdDev * sqrt(2 * M_PI))) * exp($exponent);
                    $probability += log($gaussianProb + 1e-10); // Avoid log(0)
                }
            }
            
            $probabilities[$class] = $probability;
        }
        
        // Return class with highest probability
        arsort($probabilities);
        return key($probabilities);
    }
    
    public function predictBatch(array $inputs): array
    {
        if (!$this->isTrained) {
            throw new Exception('Model not trained yet');
        }
        
        $predictions = [];
        foreach ($inputs as $input) {
            $predictions[] = $this->predict($input);
        }
        
        return $predictions;
    }
    
    public function save(string $path): void
    {
        $data = [
            'classes' => $this->classes,
            'class_probabilities' => $this->classProbabilities,
            'feature_probabilities' => $this->featureProbabilities,
            'metrics' => $this->metrics,
            'is_trained' => $this->isTrained
        ];
        
        file_put_contents($path, json_encode($data));
    }
    
    public function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new Exception("Model file not found: $path");
        }
        
        $data = json_decode(file_get_contents($path), true);
        
        $this->classes = $data['classes'] ?? [];
        $this->classProbabilities = $data['class_probabilities'] ?? [];
        $this->featureProbabilities = $data['feature_probabilities'] ?? [];
        $this->metrics = $data['metrics'] ?? [];
        $this->isTrained = $data['is_trained'] ?? false;
    }
    
    public function getMetrics(): array
    {
        return $this->metrics;
    }
}

// Regression ML Model
class RegressionMLModel implements MLModel
{
    private array $coefficients = [];
    private array $metrics = [];
    private bool $isTrained = false;
    
    public function train(array $data, array $labels): void
    {
        if (empty($data) || empty($labels)) {
            throw new Exception('Training data and labels cannot be empty');
        }
        
        if (count($data) !== count($labels)) {
            throw new Exception('Data and labels must have the same length');
        }
        
        // Multiple linear regression using normal equations
        $numSamples = count($data);
        $numFeatures = count($data[0]);
        
        // Add bias term
        $X = [];
        for ($i = 0; $i < $numSamples; $i++) {
            $row = [1]; // Bias term
            for ($j = 0; $j < $numFeatures; $j++) {
                $row[] = $data[$i][$j];
            }
            $X[] = $row;
        }
        
        $y = $labels;
        
        // Calculate coefficients using normal equations: β = (X^T * X)^(-1) * X^T * y
        $this->coefficients = $this->solveNormalEquations($X, $y);
        
        $this->isTrained = true;
        $this->metrics = [
            'coefficients' => $this->coefficients,
            'samples' => $numSamples,
            'features' => $numFeatures
        ];
        
        echo "Regression model trained\n";
    }
    
    private function solveNormalEquations(array $X, array $y): array
    {
        $numSamples = count($X);
        $numFeatures = count($X[0]);
        
        // Calculate X^T * X
        $XTX = [];
        for ($i = 0; $i < $numFeatures; $i++) {
            $XTX[$i] = array_fill(0, $numFeatures, 0);
            for ($j = 0; $j < $numFeatures; $j++) {
                $sum = 0;
                for ($k = 0; $k < $numSamples; $k++) {
                    $sum += $X[$k][$i] * $X[$k][$j];
                }
                $XTX[$i][$j] = $sum;
            }
        }
        
        // Calculate X^T * y
        $XTy = array_fill(0, $numFeatures, 0);
        for ($i = 0; $i < $numFeatures; $i++) {
            $sum = 0;
            for ($k = 0; $k < $numSamples; $k++) {
                $sum += $X[$k][$i] * $y[$k];
            }
            $XTy[$i] = $sum;
        }
        
        // Solve for coefficients (simplified - in practice, use proper matrix operations)
        return $this->gaussianElimination($XTX, $XTy);
    }
    
    private function gaussianElimination(array $A, array $b): array
    {
        $n = count($A);
        
        // Forward elimination
        for ($i = 0; $i < $n; $i++) {
            // Find pivot
            $maxRow = $i;
            for ($k = $i + 1; $k < $n; $k++) {
                if (abs($A[$k][$i]) > abs($A[$maxRow][$i])) {
                    $maxRow = $k;
                }
            }
            
            // Swap rows
            $temp = $A[$i];
            $A[$i] = $A[$maxRow];
            $A[$maxRow] = $temp;
            
            $temp = $b[$i];
            $b[$i] = $b[$maxRow];
            $b[$maxRow] = $temp;
            
            // Eliminate column
            for ($k = $i + 1; $k < $n; $k++) {
                $factor = $A[$k][$i] / $A[$i][$i];
                for ($j = $i; $j < $n; $j++) {
                    $A[$k][$j] -= $factor * $A[$i][$j];
                }
                $b[$k] -= $factor * $b[$i];
            }
        }
        
        // Back substitution
        $x = array_fill(0, $n, 0);
        for ($i = $n - 1; $i >= 0; $i--) {
            $x[$i] = $b[$i];
            for ($j = $i + 1; $j < $n; $j++) {
                $x[$i] -= $A[$i][$j] * $x[$j];
            }
            $x[$i] /= $A[$i][$i];
        }
        
        return $x;
    }
    
    public function predict(array $input): mixed
    {
        if (!$this->isTrained) {
            throw new Exception('Model not trained yet');
        }
        
        $prediction = $this->coefficients[0]; // Bias term
        
        for ($i = 0; $i < count($input); $i++) {
            $prediction += $this->coefficients[$i + 1] * $input[$i];
        }
        
        return $prediction;
    }
    
    public function predictBatch(array $inputs): array
    {
        if (!$this->isTrained) {
            throw new Exception('Model not trained yet');
        }
        
        $predictions = [];
        foreach ($inputs as $input) {
            $predictions[] = $this->predict($input);
        }
        
        return $predictions;
    }
    
    public function save(string $path): void
    {
        $data = [
            'coefficients' => $this->coefficients,
            'metrics' => $this->metrics,
            'is_trained' => $this->isTrained
        ];
        
        file_put_contents($path, json_encode($data));
    }
    
    public function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new Exception("Model file not found: $path");
        }
        
        $data = json_decode(file_get_contents($path), true);
        
        $this->coefficients = $data['coefficients'] ?? [];
        $this->metrics = $data['metrics'] ?? [];
        $this->isTrained = $data['is_trained'] ?? false;
    }
    
    public function getMetrics(): array
    {
        return $this->metrics;
    }
}

// External ML Service Integration
class ExternalMLService
{
    private string $apiUrl;
    private string $apiKey;
    private array $models = [];
    
    public function __construct(string $apiUrl, string $apiKey = '')
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }
    
    public function createModel(string $name, string $type, array $config): array
    {
        $payload = [
            'name' => $name,
            'type' => $type,
            'config' => $config
        ];
        
        $response = $this->makeRequest('POST', '/models', $payload);
        
        if ($response['success']) {
            $this->models[$name] = $response['data'];
            echo "External model created: $name\n";
        }
        
        return $response;
    }
    
    public function trainModel(string $name, array $data, array $labels): array
    {
        $payload = [
            'data' => $data,
            'labels' => $labels
        ];
        
        $response = $this->makeRequest('POST', "/models/$name/train", $payload);
        
        if ($response['success']) {
            echo "External model trained: $name\n";
        }
        
        return $response;
    }
    
    public function predict(string $name, array $input): array
    {
        $payload = [
            'input' => $input
        ];
        
        $response = $this->makeRequest('POST', "/models/$name/predict", $payload);
        
        return $response;
    }
    
    public function getModels(): array
    {
        $response = $this->makeRequest('GET', '/models');
        
        if ($response['success']) {
            $this->models = $response['data'];
        }
        
        return $response;
    }
    
    public function getModelInfo(string $name): array
    {
        $response = $this->makeRequest('GET', "/models/$name");
        
        return $response;
    }
    
    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        // Simulate API call (in real implementation, use cURL or Guzzle)
        $url = $this->apiUrl . $endpoint;
        
        echo "Making $method request to: $url\n";
        
        // Simulate response
        if ($method === 'GET' && $endpoint === '/models') {
            return [
                'success' => true,
                'data' => [
                    'model1' => ['type' => 'classification', 'status' => 'trained'],
                    'model2' => ['type' => 'regression', 'status' => 'training']
                ]
            ];
        }
        
        if ($method === 'POST' && strpos($endpoint, '/models/') === 0) {
            return [
                'success' => true,
                'data' => [
                    'id' => uniqid('model_'),
                    'name' => $data['name'] ?? 'unknown',
                    'type' => $data['type'] ?? 'unknown',
                    'status' => 'created'
                ]
            ];
        }
        
        if ($method === 'POST' && strpos($endpoint, '/train') !== false) {
            return [
                'success' => true,
                'data' => [
                    'status' => 'training_completed',
                    'accuracy' => 0.85,
                    'training_time' => 120
                ]
            ];
        }
        
        if ($method === 'POST' && strpos($endpoint, '/predict') !== false) {
            return [
                'success' => true,
                'data' => [
                    'prediction' => rand(0, 1),
                    'confidence' => rand(70, 95) / 100
                ]
            ];
        }
        
        return ['success' => false, 'error' => 'Unknown endpoint'];
    }
}

// ML Integration Examples
class MLIntegrationExamples
{
    public function demonstrateLocalModelManager(): void
    {
        echo "Local ML Model Manager Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $manager = new MLModelManager('./temp_models/');
        
        // Register models
        $simpleModel = new SimpleMLModel();
        $classificationModel = new ClassificationMLModel();
        $regressionModel = new RegressionMLModel();
        
        $manager->registerModel('simple_regression', $simpleModel, ['type' => 'simple']);
        $manager->registerModel('email_classifier', $classificationModel, ['type' => 'classification']);
        $manager->registerModel('price_predictor', $regressionModel, ['type' => 'regression']);
        
        echo "\nRegistered models: " . implode(', ', $manager->listModels()) . "\n";
        
        // Train models
        echo "\nTraining models...\n";
        
        // Training data for simple regression
        $trainData = [
            [1], [2], [3], [4], [5], [6], [7], [8], [9], [10]
        ];
        $trainLabels = [2, 4, 6, 8, 10, 12, 14, 16, 18, 20];
        
        $manager->trainModel('simple_regression', $trainData, $trainLabels);
        
        // Training data for classification
        $classData = [
            [1, 1], [2, 2], [3, 3], [4, 4], [5, 5],
            [-1, -1], [-2, -2], [-3, -3], [-4, -4], [-5, -5]
        ];
        $classLabels = ['positive', 'positive', 'positive', 'positive', 'positive',
                       'negative', 'negative', 'negative', 'negative', 'negative'];
        
        $manager->trainModel('email_classifier', $classData, $classLabels);
        
        // Make predictions
        echo "\nMaking predictions...\n";
        
        $simplePrediction = $manager->predict('simple_regression', [3]);
        echo "Simple regression prediction for [3]: $simplePrediction\n";
        
        $classPrediction = $manager->predict('email_classifier', [2, 2]);
        echo "Classification prediction for [2, 2]: $classPrediction\n";
        
        // Batch predictions
        echo "\nBatch predictions:\n";
        $batchInputs = [[1], [2], [3], [4], [5]];
        $batchPredictions = $manager->predictBatch('simple_regression', $batchInputs);
        
        foreach ($batchInputs as $i => $input) {
            echo "Input " . json_encode($input) . " -> {$batchPredictions[$i]}\n";
        }
        
        // Save models
        echo "\nSaving models...\n";
        $manager->saveModel('simple_regression');
        $manager->saveModel('email_classifier');
        
        // Get model metrics
        echo "\nModel metrics:\n";
        $metrics = $manager->getModelMetrics('simple_regression');
        foreach ($metrics as $key => $value) {
            echo "  $key: " . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
    }
    
    public function demonstrateModelPersistence(): void
    {
        echo "\nModel Persistence Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $manager = new MLModelManager('./temp_models/');
        
        // Create and train a model
        $model = new SimpleMLModel();
        $manager->registerModel('persistent_model', $model, ['type' => 'simple']);
        
        $trainData = [[1], [2], [3], [4], [5]];
        $trainLabels = [2, 4, 6, 8, 10];
        
        $manager->trainModel('persistent_model', $trainData, $trainLabels);
        
        // Make prediction before saving
        $predictionBefore = $manager->predict('persistent_model', [3]);
        echo "Prediction before saving: $predictionBefore\n";
        
        // Save model
        $manager->saveModel('persistent_model');
        
        // Clear models (simulate restart)
        $manager = new MLModelManager('./temp_models/');
        
        // Load model
        $manager->loadModel('persistent_model');
        
        // Make prediction after loading
        $predictionAfter = $manager->predict('persistent_model', [3]);
        echo "Prediction after loading: $predictionAfter\n";
        
        // Verify predictions match
        echo "Predictions match: " . ($predictionBefore == $predictionAfter ? 'Yes' : 'No') . "\n";
        
        // Show loaded model metrics
        echo "\nLoaded model metrics:\n";
        $metrics = $manager->getModelMetrics('persistent_model');
        foreach ($metrics as $key => $value) {
            echo "  $key: " . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
    }
    
    public function demonstrateExternalServiceIntegration(): void
    {
        echo "\nExternal ML Service Integration Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        $service = new ExternalMLService('https://api.ml-service.com/v1', 'api_key_123');
        
        // Get available models
        echo "Getting available models...\n";
        $modelsResponse = $service->getModels();
        
        if ($modelsResponse['success']) {
            echo "Available models:\n";
            foreach ($modelsResponse['data'] as $name => $info) {
                echo "  $name: {$info['type']} ({$info['status']})\n";
            }
        }
        
        // Create a new model
        echo "\nCreating new model...\n";
        $createResponse = $service->createModel('sentiment_analyzer', 'classification', [
            'algorithm' => 'naive_bayes',
            'features' => ['text_features'],
            'classes' => ['positive', 'negative', 'neutral']
        ]);
        
        if ($createResponse['success']) {
            echo "Model created with ID: {$createResponse['data']['id']}\n";
        }
        
        // Train the model
        echo "\nTraining model...\n";
        $trainData = [
            ['text' => 'I love this product!', 'features' => [1, 0, 1]],
            ['text' => 'This is terrible', 'features' => [0, 1, 0]],
            ['text' => 'It\'s okay', 'features' => [0, 0, 1]]
        ];
        $trainLabels = ['positive', 'negative', 'neutral'];
        
        $trainResponse = $service->trainModel('sentiment_analyzer', $trainData, $trainLabels);
        
        if ($trainResponse['success']) {
            echo "Training completed\n";
            echo "Accuracy: {$trainResponse['data']['accuracy']}\n";
            echo "Training time: {$trainResponse['data']['training_time']}s\n";
        }
        
        // Make predictions
        echo "\nMaking predictions...\n";
        $testInputs = [
            ['text' => 'Amazing product!', 'features' => [1, 0, 1]],
            ['text' => 'Worst ever', 'features' => [0, 1, 0]]
        ];
        
        foreach ($testInputs as $input) {
            $prediction = $service->predict('sentiment_analyzer', $input);
            
            if ($prediction['success']) {
                echo "Input: '{$input['text']}'\n";
                echo "Prediction: {$prediction['data']['prediction']}\n";
                echo "Confidence: " . round($prediction['data']['confidence'] * 100, 1) . "%\n\n";
            }
        }
        
        // Get model info
        echo "Getting model info...\n";
        $modelInfo = $service->getModelInfo('sentiment_analyzer');
        
        if ($modelInfo['success']) {
            echo "Model info:\n";
            foreach ($modelInfo['data'] as $key => $value) {
                echo "  $key: " . (is_array($value) ? json_encode($value) : $value) . "\n";
            }
        }
    }
    
    public function demonstrateModelComparison(): void
    {
        echo "\nModel Comparison Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $manager = new MLModelManager('./temp_models/');
        
        // Create and train multiple models
        $models = [
            'linear_model' => new SimpleMLModel(),
            'classification_model' => new ClassificationMLModel(),
            'regression_model' => new RegressionMLModel()
        ];
        
        foreach ($models as $name => $model) {
            $manager->registerModel($name, $model, ['type' => $name]);
        }
        
        // Train models with same data
        $trainData = [
            [1, 2], [2, 4], [3, 6], [4, 8], [5, 10],
            [6, 12], [7, 14], [8, 16], [9, 18], [10, 20]
        ];
        $trainLabels = [3, 6, 9, 12, 15, 18, 21, 24, 27, 30];
        
        echo "Training multiple models...\n";
        foreach ($models as $name => $model) {
            $manager->trainModel($name, $trainData, $trainLabels);
        }
        
        // Compare predictions
        echo "\nComparing predictions on test data:\n";
        $testData = [[2, 4], [5, 10], [8, 16]];
        
        foreach ($testData as $i => $input) {
            echo "\nTest input " . ($i + 1) . ": " . json_encode($input) . "\n";
            
            foreach ($models as $name => $model) {
                $prediction = $manager->predict($name, $input);
                echo "  $name: $prediction\n";
            }
        }
        
        // Compare metrics
        echo "\nModel metrics comparison:\n";
        foreach ($models as $name => $model) {
            echo "\n$name:\n";
            $metrics = $manager->getModelMetrics($name);
            foreach ($metrics as $key => $value) {
                if ($key !== 'coefficients') { // Skip long coefficient arrays
                    echo "  $key: " . (is_array($value) ? json_encode($value) : $value) . "\n";
                }
            }
        }
        
        // Performance comparison
        echo "\nPerformance comparison:\n";
        $iterations = 100;
        
        foreach ($models as $name => $model) {
            $startTime = microtime(true);
            
            for ($i = 0; $i < $iterations; $i++) {
                $manager->predict($name, [5, 10]);
            }
            
            $endTime = microtime(true);
            $avgTime = (($endTime - $startTime) / $iterations) * 1000;
            
            echo "  $name: " . round($avgTime, 3) . "ms per prediction\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nML Integration Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Model Management:\n";
        echo "   • Use consistent model interfaces\n";
        echo "   • Implement proper versioning\n";
        echo "   • Store models securely\n";
        echo "   • Monitor model performance\n";
        echo "   • Handle model failures gracefully\n\n";
        
        echo "2. Data Handling:\n";
        echo "   • Validate input data\n";
        echo "   • Handle missing values\n";
        echo "   • Scale features appropriately\n";
        echo "   • Use data pipelines\n";
        echo "   • Log data transformations\n\n";
        
        echo "3. External Services:\n";
        echo "   • Implement retry mechanisms\n";
        echo "   • Handle API rate limits\n";
        echo "   • Cache responses when appropriate\n";
        echo "   • Monitor API usage\n";
        echo "   • Use fallback models\n\n";
        
        echo "4. Performance:\n";
        echo "   • Optimize prediction speed\n";
        echo "   • Use batch predictions\n";
        echo "   • Implement caching\n";
        echo "   • Monitor resource usage\n";
        echo "   • Use asynchronous processing\n\n";
        
        echo "5. Security:\n";
        echo "   • Protect API keys\n";
        echo "   • Validate inputs\n";
        echo "   • Use secure connections\n";
        echo "   • Implement access controls\n";
        echo "   • Audit model access";
    }
    
    public function runAllExamples(): void
    {
        echo "Machine Learning Integration Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateLocalModelManager();
        $this->demonstrateModelPersistence();
        $this->demonstrateExternalServiceIntegration();
        $this->demonstrateModelComparison();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runMLIntegrationDemo(): void
{
    $examples = new MLIntegrationExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runMLIntegrationDemo();
}
?>
