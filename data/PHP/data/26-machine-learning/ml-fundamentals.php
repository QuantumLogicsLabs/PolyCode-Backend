<?php
/**
 * Machine Learning Fundamentals in PHP
 * 
 * Basic ML concepts, algorithms, and implementations in PHP.
 */

// Simple Linear Regression
class LinearRegression
{
    private array $data = [];
    private float $slope = 0.0;
    private float $intercept = 0.0;
    private array $predictions = [];
    
    public function addDataPoint(float $x, float $y): void
    {
        $this->data[] = ['x' => $x, 'y' => $y];
    }
    
    public function fit(): void
    {
        if (count($this->data) < 2) {
            throw new Exception('Need at least 2 data points for regression');
        }
        
        $n = count($this->data);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;
        
        foreach ($this->data as $point) {
            $sumX += $point['x'];
            $sumY += $point['y'];
            $sumXY += $point['x'] * $point['y'];
            $sumX2 += $point['x'] * $point['x'];
        }
        
        // Calculate slope and intercept using least squares method
        $denominator = $n * $sumX2 - $sumX * $sumX;
        
        if ($denominator == 0) {
            $this->slope = 0;
            $this->intercept = $sumY / $n;
        } else {
            $this->slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
            $this->intercept = ($sumY - $this->slope * $sumX) / $n;
        }
        
        echo "Linear Regression Model:\n";
        echo "Slope: {$this->slope}\n";
        echo "Intercept: {$this->intercept}\n";
        echo "Equation: y = {$this->slope}x + {$this->intercept}\n\n";
    }
    
    public function predict(float $x): float
    {
        return $this->slope * $x + $this->intercept;
    }
    
    public function predictBatch(array $xValues): array
    {
        $this->predictions = [];
        foreach ($xValues as $x) {
            $this->predictions[] = $this->predict($x);
        }
        return $this->predictions;
    }
    
    public function calculateMSE(): float
    {
        if (empty($this->data)) {
            return 0.0;
        }
        
        $sumSquaredErrors = 0;
        foreach ($this->data as $point) {
            $predicted = $this->predict($point['x']);
            $error = $point['y'] - $predicted;
            $sumSquaredErrors += $error * $error;
        }
        
        return $sumSquaredErrors / count($this->data);
    }
    
    public function calculateR2(): float
    {
        if (empty($this->data)) {
            return 0.0;
        }
        
        $meanY = array_sum(array_column($this->data, 'y')) / count($this->data);
        $totalSumSquares = 0;
        $residualSumSquares = 0;
        
        foreach ($this->data as $point) {
            $predicted = $this->predict($point['x']);
            $totalSumSquares += pow($point['y'] - $meanY, 2);
            $residualSumSquares += pow($point['y'] - $predicted, 2);
        }
        
        return $totalSumSquares == 0 ? 0 : 1 - ($residualSumSquares / $totalSumSquares);
    }
    
    public function getSlope(): float
    {
        return $this->slope;
    }
    
    public function getIntercept(): float
    {
        return $this->intercept;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function getPredictions(): array
    {
        return $this->predictions;
    }
}

// K-Nearest Neighbors Classifier
class KNNClassifier
{
    private array $trainingData = [];
    private int $k = 3;
    private string $distanceMetric = 'euclidean';
    
    public function __construct(int $k = 3, string $distanceMetric = 'euclidean')
    {
        $this->k = $k;
        $this->distanceMetric = $distanceMetric;
    }
    
    public function addTrainingPoint(array $features, string $label): void
    {
        $this->trainingData[] = [
            'features' => $features,
            'label' => $label
        ];
    }
    
    public function setK(int $k): void
    {
        $this->k = $k;
    }
    
    public function setDistanceMetric(string $metric): void
    {
        $this->distanceMetric = $metric;
    }
    
    public function predict(array $features): string
    {
        if (empty($this->trainingData)) {
            throw new Exception('No training data available');
        }
        
        // Calculate distances to all training points
        $distances = [];
        foreach ($this->trainingData as $index => $point) {
            $distance = $this->calculateDistance($features, $point['features']);
            $distances[] = [
                'index' => $index,
                'distance' => $distance,
                'label' => $point['label']
            ];
        }
        
        // Sort by distance
        usort($distances, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        // Get k nearest neighbors
        $nearestNeighbors = array_slice($distances, 0, $this->k);
        
        // Vote for the most common label
        $votes = [];
        foreach ($nearestNeighbors as $neighbor) {
            $label = $neighbor['label'];
            if (!isset($votes[$label])) {
                $votes[$label] = 0;
            }
            $votes[$label]++;
        }
        
        // Return the label with most votes
        arsort($votes);
        return key($votes);
    }
    
    public function predictBatch(array $featuresBatch): array
    {
        $predictions = [];
        foreach ($featuresBatch as $features) {
            $predictions[] = $this->predict($features);
        }
        return $predictions;
    }
    
    private function calculateDistance(array $a, array $b): float
    {
        if (count($a) !== count($b)) {
            throw new Exception('Feature vectors must have the same length');
        }
        
        switch ($this->distanceMetric) {
            case 'euclidean':
                return $this->euclideanDistance($a, $b);
            case 'manhattan':
                return $this->manhattanDistance($a, $b);
            case 'minkowski':
                return $this->minkowskiDistance($a, $b, 2);
            default:
                return $this->euclideanDistance($a, $b);
        }
    }
    
    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }
    
    private function manhattanDistance(array $a, array $b): float
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += abs($a[$i] - $b[$i]);
        }
        return $sum;
    }
    
    private function minkowskiDistance(array $a, array $b, int $p): float
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow(abs($a[$i] - $b[$i]), $p);
        }
        return pow($sum, 1 / $p);
    }
    
    public function getTrainingData(): array
    {
        return $this->trainingData;
    }
    
    public function getK(): int
    {
        return $this->k;
    }
}

// Naive Bayes Classifier
class NaiveBayesClassifier
{
    private array $classes = [];
    private array $featureProbabilities = [];
    private array $classProbabilities = [];
    private array $featureCounts = [];
    private int $totalSamples = 0;
    
    public function train(array $samples, array $labels): void
    {
        if (count($samples) !== count($labels)) {
            throw new Exception('Samples and labels must have the same length');
        }
        
        $this->totalSamples = count($samples);
        $this->classes = array_unique($labels);
        
        // Initialize data structures
        foreach ($this->classes as $class) {
            $this->classProbabilities[$class] = 0;
            $this->featureCounts[$class] = [];
        }
        
        // Count samples per class
        $classCounts = array_count_values($labels);
        
        // Calculate class probabilities
        foreach ($this->classes as $class) {
            $this->classProbabilities[$class] = $classCounts[$class] / $this->totalSamples;
        }
        
        // Count feature occurrences per class
        foreach ($samples as $index => $sample) {
            $class = $labels[$index];
            
            foreach ($sample as $feature => $value) {
                if (!isset($this->featureCounts[$class][$feature])) {
                    $this->featureCounts[$class][$feature] = [];
                }
                
                if (!isset($this->featureCounts[$class][$feature][$value])) {
                    $this->featureCounts[$class][$feature][$value] = 0;
                }
                
                $this->featureCounts[$class][$feature][$value]++;
            }
        }
        
        // Calculate feature probabilities with Laplace smoothing
        foreach ($this->classes as $class) {
            foreach ($this->featureCounts[$class] as $feature => $values) {
                $totalValues = array_sum($values);
                $uniqueValues = count($values);
                
                foreach ($values as $value => $count) {
                    // Laplace smoothing: (count + 1) / (total + unique_values)
                    $this->featureProbabilities[$class][$feature][$value] = 
                        ($count + 1) / ($totalValues + $uniqueValues);
                }
            }
        }
        
        echo "Naive Bayes Classifier trained on {$this->totalSamples} samples\n";
        echo "Classes: " . implode(', ', $this->classes) . "\n\n";
    }
    
    public function predict(array $sample): string
    {
        if (empty($this->classProbabilities)) {
            throw new Exception('Classifier not trained yet');
        }
        
        $probabilities = [];
        
        foreach ($this->classes as $class) {
            $probability = log($this->classProbabilities[$class]);
            
            foreach ($sample as $feature => $value) {
                if (isset($this->featureProbabilities[$class][$feature][$value])) {
                    $probability += log($this->featureProbabilities[$class][$feature][$value]);
                } else {
                    // Apply Laplace smoothing for unseen values
                    $probability += log(1 / ($this->totalSamples + 1));
                }
            }
            
            $probabilities[$class] = $probability;
        }
        
        // Return the class with highest probability
        arsort($probabilities);
        return key($probabilities);
    }
    
    public function predictBatch(array $samples): array
    {
        $predictions = [];
        foreach ($samples as $sample) {
            $predictions[] = $this->predict($sample);
        }
        return $predictions;
    }
    
    public function getClasses(): array
    {
        return $this->classes;
    }
    
    public function getClassProbabilities(): array
    {
        return $this->classProbabilities;
    }
    
    public function getFeatureProbabilities(): array
    {
        return $this->featureProbabilities;
    }
}

// K-Means Clustering
class KMeans
{
    private array $data = [];
    private int $k = 3;
    private array $centroids = [];
    private array $clusters = [];
    private int $maxIterations = 100;
    private float $tolerance = 0.001;
    
    public function __construct(int $k = 3, int $maxIterations = 100, float $tolerance = 0.001)
    {
        $this->k = $k;
        $this->maxIterations = $maxIterations;
        $this->tolerance = $tolerance;
    }
    
    public function addDataPoint(array $point): void
    {
        $this->data[] = $point;
    }
    
    public function fit(): void
    {
        if (count($this->data) < $this->k) {
            throw new Exception('Need at least k data points for clustering');
        }
        
        // Initialize centroids randomly
        $this->initializeCentroids();
        
        for ($iteration = 0; $iteration < $this->maxIterations; $iteration++) {
            // Assign points to nearest centroid
            $this->assignPointsToClusters();
            
            // Update centroids
            $newCentroids = $this->updateCentroids();
            
            // Check for convergence
            if ($this->hasConverged($this->centroids, $newCentroids)) {
                echo "K-Means converged after $iteration iterations\n";
                break;
            }
            
            $this->centroids = $newCentroids;
        }
        
        echo "K-Means clustering completed\n";
        echo "Final centroids:\n";
        foreach ($this->centroids as $index => $centroid) {
            echo "Cluster $index: [" . implode(', ', $centroid) . "]\n";
        }
        echo "\n";
    }
    
    private function initializeCentroids(): void
    {
        // Randomly select k points as initial centroids
        $indices = array_rand($this->data, $this->k);
        
        foreach ($indices as $index) {
            $this->centroids[] = $this->data[$index];
        }
    }
    
    private function assignPointsToClusters(): void
    {
        $this->clusters = array_fill(0, $this->k, []);
        
        foreach ($this->data as $point) {
            $nearestCentroid = $this->findNearestCentroid($point);
            $this->clusters[$nearestCentroid][] = $point;
        }
    }
    
    private function findNearestCentroid(array $point): int
    {
        $minDistance = INF;
        $nearestCentroid = 0;
        
        foreach ($this->centroids as $index => $centroid) {
            $distance = $this->euclideanDistance($point, $centroid);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestCentroid = $index;
            }
        }
        
        return $nearestCentroid;
    }
    
    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }
    
    private function updateCentroids(): array
    {
        $newCentroids = [];
        
        for ($i = 0; $i < $this->k; $i++) {
            if (empty($this->clusters[$i])) {
                // If cluster is empty, keep the old centroid
                $newCentroids[] = $this->centroids[$i];
                continue;
            }
            
            $clusterSize = count($this->clusters[$i]);
            $dimensions = count($this->clusters[$i][0]);
            $newCentroid = array_fill(0, $dimensions, 0);
            
            // Calculate mean of all points in cluster
            foreach ($this->clusters[$i] as $point) {
                for ($j = 0; $j < $dimensions; $j++) {
                    $newCentroid[$j] += $point[$j];
                }
            }
            
            for ($j = 0; $j < $dimensions; $j++) {
                $newCentroid[$j] /= $clusterSize;
            }
            
            $newCentroids[] = $newCentroid;
        }
        
        return $newCentroids;
    }
    
    private function hasConverged(array $oldCentroids, array $newCentroids): bool
    {
        for ($i = 0; $i < $this->k; $i++) {
            $distance = $this->euclideanDistance($oldCentroids[$i], $newCentroids[$i]);
            if ($distance > $this->tolerance) {
                return false;
            }
        }
        return true;
    }
    
    public function predict(array $point): int
    {
        if (empty($this->centroids)) {
            throw new Exception('Model not trained yet');
        }
        
        return $this->findNearestCentroid($point);
    }
    
    public function getCentroids(): array
    {
        return $this->centroids;
    }
    
    public function getClusters(): array
    {
        return $this->clusters;
    }
    
    public function getInertia(): float
    {
        $inertia = 0;
        
        foreach ($this->clusters as $index => $cluster) {
            $centroid = $this->centroids[$index];
            
            foreach ($cluster as $point) {
                $inertia += pow($this->euclideanDistance($point, $centroid), 2);
            }
        }
        
        return $inertia;
    }
}

// Simple Neural Network
class SimpleNeuralNetwork
{
    private array $weights = [];
    private array $biases = [];
    private array $layers = [];
    private float $learningRate = 0.1;
    private int $epochs = 1000;
    
    public function __construct(array $layers, float $learningRate = 0.1, int $epochs = 1000)
    {
        $this->layers = $layers;
        $this->learningRate = $learningRate;
        $this->epochs = $epochs;
        $this->initializeNetwork();
    }
    
    private function initializeNetwork(): void
    {
        // Initialize weights and biases
        for ($i = 1; $i < count($this->layers); $i++) {
            $inputSize = $this->layers[$i - 1];
            $outputSize = $this->layers[$i];
            
            // Initialize weights with random values between -1 and 1
            $this->weights[$i] = [];
            for ($j = 0; $j < $inputSize; $j++) {
                $this->weights[$i][$j] = [];
                for ($k = 0; $k < $outputSize; $k++) {
                    $this->weights[$i][$j][$k] = (rand(0, 2000) - 1000) / 1000;
                }
            }
            
            // Initialize biases with random values
            $this->biases[$i] = [];
            for ($j = 0; $j < $outputSize; $j++) {
                $this->biases[$i][$j] = (rand(0, 2000) - 1000) / 1000;
            }
        }
    }
    
    public function train(array $inputs, array $targets): void
    {
        echo "Training neural network...\n";
        
        for ($epoch = 0; $epoch < $this->epochs; $epoch++) {
            $totalError = 0;
            
            for ($i = 0; $i < count($inputs); $i++) {
                $input = $inputs[$i];
                $target = $targets[$i];
                
                // Forward propagation
                $activations = $this->forwardPropagation($input);
                
                // Calculate error
                $output = $activations[count($activations) - 1];
                $error = $this->calculateError($output, $target);
                $totalError += array_sum(array_map(fn($e) => $e * $e, $error));
                
                // Backward propagation
                $this->backwardPropagation($activations, $target);
            }
            
            if ($epoch % 100 === 0) {
                echo "Epoch $epoch, Error: " . ($totalError / count($inputs)) . "\n";
            }
        }
        
        echo "Training completed!\n\n";
    }
    
    private function forwardPropagation(array $input): array
    {
        $activations = [$input];
        
        for ($i = 1; $i < count($this->layers); $i++) {
            $previousActivation = $activations[$i - 1];
            $currentActivation = [];
            
            for ($j = 0; $j < $this->layers[$i]; $j++) {
                $sum = $this->biases[$i][$j];
                
                for ($k = 0; $k < count($previousActivation); $k++) {
                    $sum += $previousActivation[$k] * $this->weights[$i][$k][$j];
                }
                
                $currentActivation[$j] = $this->sigmoid($sum);
            }
            
            $activations[] = $currentActivation;
        }
        
        return $activations;
    }
    
    private function backwardPropagation(array $activations, array $target): void
    {
        $layers = count($this->layers);
        $deltas = array_fill(0, $layers, []);
        
        // Calculate output layer delta
        $output = $activations[$layers - 1];
        for ($i = 0; $i < $this->layers[$layers - 1]; $i++) {
            $error = $output[$i] - $target[$i];
            $deltas[$layers - 1][$i] = $error * $this->sigmoidDerivative($output[$i]);
        }
        
        // Calculate hidden layer deltas
        for ($i = $layers - 2; $i >= 1; $i--) {
            for ($j = 0; $j < $this->layers[$i]; $j++) {
                $delta = 0;
                
                for ($k = 0; $k < $this->layers[$i + 1]; $k++) {
                    $delta += $deltas[$i + 1][$k] * $this->weights[$i + 1][$j][$k];
                }
                
                $activation = $activations[$i][$j];
                $deltas[$i][$j] = $delta * $this->sigmoidDerivative($activation);
            }
        }
        
        // Update weights and biases
        for ($i = 1; $i < $layers; $i++) {
            for ($j = 0; $j < $this->layers[$i - 1]; $j++) {
                for ($k = 0; $k < $this->layers[$i]; $k++) {
                    $this->weights[$i][$j][$k] -= $this->learningRate * 
                        $deltas[$i][$k] * $activations[$i - 1][$j];
                }
            }
            
            for ($j = 0; $j < $this->layers[$i]; $j++) {
                $this->biases[$i][$j] -= $this->learningRate * $deltas[$i][$j];
            }
        }
    }
    
    public function predict(array $input): array
    {
        $activations = $this->forwardPropagation($input);
        return $activations[count($activations) - 1];
    }
    
    public function predictBatch(array $inputs): array
    {
        $predictions = [];
        foreach ($inputs as $input) {
            $predictions[] = $this->predict($input);
        }
        return $predictions;
    }
    
    private function sigmoid(float $x): float
    {
        return 1 / (1 + exp(-$x));
    }
    
    private function sigmoidDerivative(float $x): float
    {
        return $x * (1 - $x);
    }
    
    private function calculateError(array $output, array $target): array
    {
        $error = [];
        for ($i = 0; $i < count($output); $i++) {
            $error[] = $output[$i] - $target[$i];
        }
        return $error;
    }
    
    public function getWeights(): array
    {
        return $this->weights;
    }
    
    public function getBiases(): array
    {
        return $this->biases;
    }
}

// ML Fundamentals Examples
class MLFundamentalsExamples
{
    public function demonstrateLinearRegression(): void
    {
        echo "Linear Regression Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $regression = new LinearRegression();
        
        // Add training data (study hours vs exam scores)
        echo "Training data (study hours vs exam scores):\n";
        $trainingData = [
            [1, 65], [2, 70], [3, 75], [4, 80], [5, 85],
            [6, 88], [7, 92], [8, 95], [9, 97], [10, 99]
        ];
        
        foreach ($trainingData as $data) {
            $regression->addDataPoint($data[0], $data[1]);
            echo "  {$data[0]} hours -> {$data[1]} score\n";
        }
        
        echo "\n";
        $regression->fit();
        
        // Make predictions
        echo "Predictions:\n";
        $testHours = [2.5, 5.5, 7.5, 11];
        $predictions = $regression->predictBatch($testHours);
        
        foreach ($testHours as $i => $hours) {
            echo "  $hours hours -> {$predictions[$i]} score\n";
        }
        
        // Calculate metrics
        echo "\nModel Performance:\n";
        echo "MSE: " . $regression->calculateMSE() . "\n";
        echo "R²: " . $regression->calculateR2() . "\n";
    }
    
    public function demonstrateKNN(): void
    {
        echo "\nK-Nearest Neighbors Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $knn = new KNNClassifier(3, 'euclidean');
        
        // Training data (features: [height, weight], label: gender)
        echo "Training data:\n";
        $trainingData = [
            [[170, 65], 'male'],
            [[175, 70], 'male'],
            [[180, 75], 'male'],
            [[165, 60], 'female'],
            [[160, 55], 'female'],
            [[155, 50], 'female']
        ];
        
        foreach ($trainingData as $data) {
            $knn->addTrainingPoint($data[0], $data[1]);
            echo "  [" . implode(', ', $data[0]) . "] -> {$data[1]}\n";
        }
        
        // Test data
        echo "\nPredictions:\n";
        $testData = [
            [168, 62],  // Should be male
            [162, 58],  // Should be female
            [178, 72]   // Should be male
        ];
        
        foreach ($testData as $features) {
            $prediction = $knn->predict($features);
            echo "  [" . implode(', ', $features) . "] -> $prediction\n";
        }
        
        // Test different k values
        echo "\nTesting different k values:\n";
        $testPoint = [168, 62];
        
        for ($k = 1; $k <= 5; $k++) {
            $knn->setK($k);
            $prediction = $knn->predict($testPoint);
            echo "  k=$k: [" . implode(', ', $testPoint) . "] -> $prediction\n";
        }
    }
    
    public function demonstrateNaiveBayes(): void
    {
        echo "\nNaive Bayes Classifier Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $naiveBayes = new NaiveBayesClassifier();
        
        // Training data (email classification)
        echo "Training data (email classification):\n";
        $samples = [
            ['free', 'money', 'win', 'prize'],
            ['buy', 'now', 'discount', 'sale'],
            ['meeting', 'project', 'deadline', 'team'],
            ['hello', 'friend', 'coffee', 'weekend'],
            ['urgent', 'meeting', 'please', 'reply'],
            ['congratulations', 'winner', 'claim', 'prize']
        ];
        
        $labels = ['spam', 'spam', 'ham', 'ham', 'ham', 'spam'];
        
        foreach ($samples as $i => $sample) {
            echo "  [" . implode(', ', $sample) . "] -> {$labels[$i]}\n";
        }
        
        $naiveBayes->train($samples, $labels);
        
        // Test data
        echo "\nPredictions:\n";
        $testEmails = [
            ['free', 'prize', 'winner'],
            ['meeting', 'project', 'team'],
            ['buy', 'discount', 'sale'],
            ['hello', 'coffee', 'friend']
        ];
        
        foreach ($testEmails as $email) {
            $prediction = $naiveBayes->predict($email);
            echo "  [" . implode(', ', $email) . "] -> $prediction\n";
        }
        
        // Show class probabilities
        echo "\nClass Probabilities:\n";
        foreach ($naiveBayes->getClassProbabilities() as $class => $probability) {
            echo "  $class: " . round($probability, 3) . "\n";
        }
    }
    
    public function demonstrateKMeans(): void
    {
        echo "\nK-Means Clustering Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $kmeans = new KMeans(3, 100, 0.001);
        
        // Generate sample data (2D points)
        echo "Data points:\n";
        $dataPoints = [
            [1, 1], [1.5, 2], [3, 4], [5, 7], [3.5, 5],
            [4.5, 5], [3.5, 4.5], [2, 2], [1, 3], [2, 1],
            [5, 5], [6, 6], [7, 7], [8, 8], [9, 9]
        ];
        
        foreach ($dataPoints as $point) {
            $kmeans->addDataPoint($point);
            echo "  [" . implode(', ', $point) . "]\n";
        }
        
        $kmeans->fit();
        
        // Show cluster assignments
        echo "\nCluster assignments:\n";
        $clusters = $kmeans->getClusters();
        
        foreach ($clusters as $index => $cluster) {
            echo "Cluster $index (" . count($cluster) . " points):\n";
            foreach ($cluster as $point) {
                echo "  [" . implode(', ', $point) . "]\n";
            }
            echo "\n";
        }
        
        echo "Inertia: " . $kmeans->getInertia() . "\n";
        
        // Predict new points
        echo "Predictions for new points:\n";
        $newPoints = [[2, 2], [6, 6], [8, 8]];
        
        foreach ($newPoints as $point) {
            $cluster = $kmeans->predict($point);
            echo "  [" . implode(', ', $point) . "] -> Cluster $cluster\n";
        }
    }
    
    public function demonstrateNeuralNetwork(): void
    {
        echo "\nSimple Neural Network Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create neural network: 2 inputs, 4 hidden neurons, 1 output
        $nn = new SimpleNeuralNetwork([2, 4, 1], 0.5, 1000);
        
        // XOR problem training data
        echo "Training data (XOR problem):\n";
        $inputs = [
            [0, 0],
            [0, 1],
            [1, 0],
            [1, 1]
        ];
        
        $targets = [
            [0],
            [1],
            [1],
            [0]
        ];
        
        foreach ($inputs as $i => $input) {
            echo "  [" . implode(', ', $input) . "] -> " . $targets[$i][0] . "\n";
        }
        
        echo "\n";
        $nn->train($inputs, $targets);
        
        // Test predictions
        echo "Predictions:\n";
        $predictions = $nn->predictBatch($inputs);
        
        foreach ($inputs as $i => $input) {
            $predicted = round($predictions[$i][0], 3);
            $actual = $targets[$i][0];
            echo "  [" . implode(', ', $input) . "] -> $predicted (actual: $actual)\n";
        }
        
        // Test with new inputs
        echo "\nNew predictions:\n";
        $newInputs = [
            [0.1, 0.9],
            [0.9, 0.1],
            [0.5, 0.5]
        ];
        
        foreach ($newInputs as $input) {
            $prediction = $nn->predict($input);
            echo "  [" . implode(', ', $input) . "] -> " . round($prediction[0], 3) . "\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nMachine Learning Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Data Preparation:\n";
        echo "   • Clean and preprocess data\n";
        echo "   • Handle missing values appropriately\n";
        echo "   • Normalize/scale features\n";
        echo "   • Split data into train/test sets\n";
        echo "   • Use cross-validation for evaluation\n\n";
        
        echo "2. Model Selection:\n";
        echo "   • Choose appropriate algorithm for the problem\n";
        echo "   • Consider model complexity vs performance\n";
        echo "   • Start with simple models\n";
        echo "   • Use proper evaluation metrics\n";
        echo "   • Compare multiple models\n\n";
        
        echo "3. Training:\n";
        echo "   • Use appropriate learning rates\n";
        echo "   • Monitor for overfitting\n";
        echo "   • Use regularization techniques\n";
        echo "   • Save model checkpoints\n";
        echo "   • Log training progress\n\n";
        
        echo "4. Evaluation:\n";
        echo "   • Use appropriate metrics\n";
        echo "   • Test on unseen data\n";
        echo "   • Analyze confusion matrices\n";
        echo "   • Check ROC curves\n";
        echo "   • Perform error analysis\n\n";
        
        echo "5. Deployment:\n";
        echo "   • Optimize model for production\n";
        echo "   • Monitor model performance\n";
        echo "   • Implement model versioning\n";
        echo "   • Plan for model updates\n";
        echo "   • Document model behavior";
    }
    
    public function runAllExamples(): void
    {
        echo "Machine Learning Fundamentals Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateLinearRegression();
        $this->demonstrateKNN();
        $this->demonstrateNaiveBayes();
        $this->demonstrateKMeans();
        $this->demonstrateNeuralNetwork();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runMLFundamentalsDemo(): void
{
    $examples = new MLFundamentalsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runMLFundamentalsDemo();
}
?>
