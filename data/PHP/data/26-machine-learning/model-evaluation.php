<?php
/**
 * Model Evaluation Metrics
 * 
 * Comprehensive evaluation metrics for machine learning models.
 */

// Classification Metrics
class ClassificationMetrics
{
    private array $trueLabels = [];
    private array $predictedLabels = [];
    private array $probabilities = [];
    private array $classes = [];
    
    public function __construct(array $trueLabels, array $predictedLabels, array $probabilities = [])
    {
        if (count($trueLabels) !== count($predictedLabels)) {
            throw new Exception('True labels and predicted labels must have the same length');
        }
        
        $this->trueLabels = $trueLabels;
        $this->predictedLabels = $predictedLabels;
        $this->probabilities = $probabilities;
        $this->classes = array_unique(array_merge($trueLabels, $predictedLabels));
    }
    
    public function getConfusionMatrix(): array
    {
        $matrix = [];
        
        foreach ($this->classes as $trueClass) {
            $matrix[$trueClass] = [];
            foreach ($this->classes as $predClass) {
                $matrix[$trueClass][$predClass] = 0;
            }
        }
        
        for ($i = 0; $i < count($this->trueLabels); $i++) {
            $true = $this->trueLabels[$i];
            $pred = $this->predictedLabels[$i];
            $matrix[$true][$pred]++;
        }
        
        return $matrix;
    }
    
    public function getAccuracy(): float
    {
        $correct = 0;
        for ($i = 0; $i < count($this->trueLabels); $i++) {
            if ($this->trueLabels[$i] === $this->predictedLabels[$i]) {
                $correct++;
            }
        }
        return $correct / count($this->trueLabels);
    }
    
    public function getPrecision(string $class = null): array
    {
        $precision = [];
        $matrix = $this->getConfusionMatrix();
        
        if ($class === null) {
            foreach ($this->classes as $c) {
                $truePositive = $matrix[$c][$c] ?? 0;
                $falsePositive = 0;
                
                foreach ($this->classes as $otherClass) {
                    if ($otherClass !== $c) {
                        $falsePositive += $matrix[$otherClass][$c] ?? 0;
                    }
                }
                
                $precision[$c] = ($truePositive + $falsePositive) > 0 ? 
                    $truePositive / ($truePositive + $falsePositive) : 0;
            }
        } else {
            $truePositive = $matrix[$class][$class] ?? 0;
            $falsePositive = 0;
            
            foreach ($this->classes as $otherClass) {
                if ($otherClass !== $class) {
                    $falsePositive += $matrix[$otherClass][$class] ?? 0;
                }
            }
            
            $precision[$class] = ($truePositive + $falsePositive) > 0 ? 
                $truePositive / ($truePositive + $falsePositive) : 0;
        }
        
        return $precision;
    }
    
    public function getRecall(string $class = null): array
    {
        $recall = [];
        $matrix = $this->getConfusionMatrix();
        
        if ($class === null) {
            foreach ($this->classes as $c) {
                $truePositive = $matrix[$c][$c] ?? 0;
                $falseNegative = 0;
                
                foreach ($this->classes as $otherClass) {
                    if ($otherClass !== $c) {
                        $falseNegative += $matrix[$c][$otherClass] ?? 0;
                    }
                }
                
                $recall[$c] = ($truePositive + $falseNegative) > 0 ? 
                    $truePositive / ($truePositive + $falseNegative) : 0;
            }
        } else {
            $truePositive = $matrix[$class][$class] ?? 0;
            $falseNegative = 0;
            
            foreach ($this->classes as $otherClass) {
                if ($otherClass !== $class) {
                    $falseNegative += $matrix[$class][$otherClass] ?? 0;
                }
            }
            
            $recall[$class] = ($truePositive + $falseNegative) > 0 ? 
                $truePositive / ($truePositive + $falseNegative) : 0;
        }
        
        return $recall;
    }
    
    public function getF1Score(string $class = null): array
    {
        $precision = $this->getPrecision($class);
        $recall = $this->getRecall($class);
        $f1 = [];
        
        foreach ($precision as $c => $p) {
            $r = $recall[$c];
            $f1[$c] = ($p + $r) > 0 ? 2 * ($p * $r) / ($p + $r) : 0;
        }
        
        return $f1;
    }
    
    public function getMacroAverage(): array
    {
        $precision = $this->getPrecision();
        $recall = $this->getRecall();
        $f1 = $this->getF1Score();
        
        return [
            'precision' => array_sum($precision) / count($precision),
            'recall' => array_sum($recall) / count($recall),
            'f1' => array_sum($f1) / count($f1)
        ];
    }
    
    public function getWeightedAverage(): array
    {
        $precision = $this->getPrecision();
        $recall = $this->getRecall();
        $f1 = $this->getF1Score();
        
        $support = $this->getSupport();
        $totalSupport = array_sum($support);
        
        $weightedPrecision = 0;
        $weightedRecall = 0;
        $weightedF1 = 0;
        
        foreach ($this->classes as $class) {
            $weight = $support[$class] / $totalSupport;
            $weightedPrecision += $precision[$class] * $weight;
            $weightedRecall += $recall[$class] * $weight;
            $weightedF1 += $f1[$class] * $weight;
        }
        
        return [
            'precision' => $weightedPrecision,
            'recall' => $weightedRecall,
            'f1' => $weightedF1
        ];
    }
    
    public function getSupport(): array
    {
        $support = [];
        
        foreach ($this->classes as $class) {
            $support[$class] = 0;
            foreach ($this->trueLabels as $label) {
                if ($label === $class) {
                    $support[$class]++;
                }
            }
        }
        
        return $support;
    }
    
    public function getROC_AUC(string $positiveClass = '1'): float
    {
        if (empty($this->probabilities)) {
            throw new Exception('Probabilities required for ROC AUC calculation');
        }
        
        $tpr = [];
        $fpr = [];
        
        // Get probabilities for positive class
        $positiveProbs = [];
        for ($i = 0; $i < count($this->trueLabels); $i++) {
            if (isset($this->probabilities[$i][$positiveClass])) {
                $positiveProbs[] = [
                    'probability' => $this->probabilities[$i][$positiveClass],
                    'actual' => $this->trueLabels[$i] === $positiveClass
                ];
            }
        }
        
        // Sort by probability
        usort($positiveProbs, function($a, $b) {
            return $b['probability'] <=> $a['probability'];
        });
        
        $tp = 0;
        $fp = 0;
        $totalPositives = 0;
        $totalNegatives = 0;
        
        foreach ($positiveProbs as $item) {
            if ($item['actual']) {
                $totalPositives++;
            } else {
                $totalNegatives++;
            }
        }
        
        foreach ($positiveProbs as $item) {
            if ($item['actual']) {
                $tp++;
            } else {
                $fp++;
            }
            
            $tpr[] = $totalPositives > 0 ? $tp / $totalPositives : 0;
            $fpr[] = $totalNegatives > 0 ? $fp / $totalNegatives : 0;
        }
        
        // Calculate AUC using trapezoidal rule
        $auc = 0;
        for ($i = 1; $i < count($tpr); $i++) {
            $auc += ($tpr[$i] + $tpr[$i - 1]) * ($fpr[$i - 1] - $fpr[$i]) / 2;
        }
        
        return $auc;
    }
    
    public function getLogLoss(): float
    {
        if (empty($this->probabilities)) {
            throw new Exception('Probabilities required for log loss calculation');
        }
        
        $logLoss = 0;
        
        for ($i = 0; $i < count($this->trueLabels); $i++) {
            $trueClass = $this->trueLabels[$i];
            
            if (isset($this->probabilities[$i][$trueClass])) {
                $prob = $this->probabilities[$i][$trueClass];
                $prob = max(min($prob, 1 - 1e-15), 1e-15); // Avoid log(0)
                $logLoss -= log($prob);
            }
        }
        
        return $logLoss / count($this->trueLabels);
    }
    
    public function getClassificationReport(): array
    {
        $precision = $this->getPrecision();
        $recall = $this->getRecall();
        $f1 = $this->getF1Score();
        $support = $this->getSupport();
        $macroAvg = $this->getMacroAverage();
        $weightedAvg = $this->getWeightedAverage();
        
        $report = [];
        
        foreach ($this->classes as $class) {
            $report[$class] = [
                'precision' => round($precision[$class], 3),
                'recall' => round($recall[$class], 3),
                'f1-score' => round($f1[$class], 3),
                'support' => $support[$class]
            ];
        }
        
        $report['macro avg'] = [
            'precision' => round($macroAvg['precision'], 3),
            'recall' => round($macroAvg['recall'], 3),
            'f1-score' => round($macroAvg['f1'], 3),
            'support' => array_sum($support)
        ];
        
        $report['weighted avg'] = [
            'precision' => round($weightedAvg['precision'], 3),
            'recall' => round($weightedAvg['recall'], 3),
            'f1-score' => round($weightedAvg['f1'], 3),
            'support' => array_sum($support)
        ];
        
        $report['accuracy'] = round($this->getAccuracy(), 3);
        
        return $report;
    }
}

// Regression Metrics
class RegressionMetrics
{
    private array $trueValues = [];
    private array $predictedValues = [];
    
    public function __construct(array $trueValues, array $predictedValues)
    {
        if (count($trueValues) !== count($predictedValues)) {
            throw new Exception('True values and predicted values must have the same length');
        }
        
        $this->trueValues = $trueValues;
        $this->predictedValues = $predictedValues;
    }
    
    public function getMAE(): float
    {
        $mae = 0;
        
        for ($i = 0; $i < count($this->trueValues); $i++) {
            $mae += abs($this->trueValues[$i] - $this->predictedValues[$i]);
        }
        
        return $mae / count($this->trueValues);
    }
    
    public function getMSE(): float
    {
        $mse = 0;
        
        for ($i = 0; $i < count($this->trueValues); $i++) {
            $mse += pow($this->trueValues[$i] - $this->predictedValues[$i], 2);
        }
        
        return $mse / count($this->trueValues);
    }
    
    public function getRMSE(): float
    {
        return sqrt($this->getMSE());
    }
    
    public function getR2(): float
    {
        $meanY = array_sum($this->trueValues) / count($this->trueValues);
        $totalSumSquares = 0;
        $residualSumSquares = 0;
        
        for ($i = 0; $i < count($this->trueValues); $i++) {
            $totalSumSquares += pow($this->trueValues[$i] - $meanY, 2);
            $residualSumSquares += pow($this->trueValues[$i] - $this->predictedValues[$i], 2);
        }
        
        return $totalSumSquares == 0 ? 0 : 1 - ($residualSumSquares / $totalSumSquares);
    }
    
    public function getAdjustedR2(int $nFeatures): float
    {
        $r2 = $this->getR2();
        $n = count($this->trueValues);
        
        if ($n <= $nFeatures + 1) {
            return 0;
        }
        
        return 1 - ((1 - $r2) * ($n - 1)) / ($n - $nFeatures - 1);
    }
    
    public function getMAPE(): float
    {
        $mape = 0;
        
        for ($i = 0; $i < count($this->trueValues); $i++) {
            if ($this->trueValues[$i] != 0) {
                $mape += abs(($this->trueValues[$i] - $this->predictedValues[$i]) / $this->trueValues[$i]);
            }
        }
        
        return ($mape / count($this->trueValues)) * 100;
    }
    
    public function getMetrics(): array
    {
        return [
            'mae' => round($this->getMAE(), 4),
            'mse' => round($this->getMSE(), 4),
            'rmse' => round($this->getRMSE(), 4),
            'r2' => round($this->getR2(), 4),
            'mape' => round($this->getMAPE(), 2)
        ];
    }
}

// Cross-Validation
class CrossValidation
{
    private array $data = [];
    private array $labels = [];
    private int $folds = 5;
    
    public function __construct(array $data, array $labels, int $folds = 5)
    {
        if (count($data) !== count($labels)) {
            throw new Exception('Data and labels must have the same length');
        }
        
        $this->data = $data;
        $this->labels = $labels;
        $this->folds = $folds;
    }
    
    public function getFolds(): array
    {
        $foldSize = floor(count($this->data) / $this->folds);
        $folds = [];
        
        // Shuffle data
        $indices = array_keys($this->data);
        shuffle($indices);
        
        for ($i = 0; $i < $this->folds; $i++) {
            $start = $i * $foldSize;
            $end = ($i == $this->folds - 1) ? count($indices) : ($i + 1) * $foldSize;
            
            $testIndices = array_slice($indices, $start, $end - $start);
            $trainIndices = array_diff($indices, $testIndices);
            
            $trainData = [];
            $trainLabels = [];
            $testData = [];
            $testLabels = [];
            
            foreach ($trainIndices as $index) {
                $trainData[] = $this->data[$index];
                $trainLabels[] = $this->labels[$index];
            }
            
            foreach ($testIndices as $index) {
                $testData[] = $this->data[$index];
                $testLabels[] = $this->labels[$index];
            }
            
            $folds[] = [
                'train_data' => $trainData,
                'train_labels' => $trainLabels,
                'test_data' => $testData,
                'test_labels' => $testLabels
            ];
        }
        
        return $folds;
    }
    
    public function stratifiedKFolds(): array
    {
        // Group by class
        $classGroups = [];
        foreach ($this->labels as $index => $label) {
            $classGroups[$label][] = $index;
        }
        
        $folds = array_fill(0, $this->folds, [
            'train_data' => [],
            'train_labels' => [],
            'test_data' => [],
            'test_labels' => []
        ]);
        
        // Distribute samples from each class evenly
        foreach ($classGroups as $class => $indices) {
            shuffle($indices);
            $foldSize = floor(count($indices) / $this->folds);
            
            for ($i = 0; $i < $this->folds; $i++) {
                $start = $i * $foldSize;
                $end = ($i == $this->folds - 1) ? count($indices) : ($i + 1) * $foldSize;
                
                $testIndices = array_slice($indices, $start, $end - $start);
                $trainIndices = array_diff($indices, $testIndices);
                
                // Add to fold
                foreach ($testIndices as $index) {
                    $folds[$i]['test_data'][] = $this->data[$index];
                    $folds[$i]['test_labels'][] = $this->labels[$index];
                }
                
                foreach ($trainIndices as $index) {
                    $folds[$i]['train_data'][] = $this->data[$index];
                    $folds[$i]['train_labels'][] = $this->labels[$index];
                }
            }
        }
        
        return $folds;
    }
    
    public function evaluate(callable $modelFunction, array $evaluationFunction): array
    {
        $folds = $this->getFolds();
        $scores = [];
        
        foreach ($folds as $fold) {
            $model = $modelFunction($fold['train_data'], $fold['train_labels']);
            $predictions = $model->predict($fold['test_data']);
            
            $score = $evaluationFunction($fold['test_labels'], $predictions);
            $scores[] = $score;
        }
        
        return [
            'scores' => $scores,
            'mean' => array_sum($scores) / count($scores),
            'std' => sqrt(array_sum(array_map(fn($s) => pow($s - (array_sum($scores) / count($scores)), 2), $scores)) / count($scores))
        ];
    }
}

// Model Comparison
class ModelComparison
{
    private array $models = [];
    private array $metrics = [];
    
    public function addModel(string $name, array $metrics): void
    {
        $this->models[$name] = $metrics;
    }
    
    public function compareModels(string $metric = 'accuracy'): array
    {
        $comparison = [];
        
        foreach ($this->models as $name => $modelMetrics) {
            if (isset($modelMetrics[$metric])) {
                $comparison[$name] = $modelMetrics[$metric];
            }
        }
        
        arsort($comparison);
        return $comparison;
    }
    
    public function getBestModel(string $metric = 'accuracy'): string
    {
        $comparison = $this->compareModels($metric);
        return key($comparison);
    }
    
    public function getStatisticalTest(string $model1, string $model2, array $scores1, array $scores2): array
    {
        // Simplified paired t-test
        $n = count($scores1);
        
        if (count($scores2) !== $n) {
            throw new Exception('Score arrays must have the same length');
        }
        
        $differences = [];
        for ($i = 0; $i < $n; $i++) {
            $differences[] = $scores1[$i] - $scores2[$i];
        }
        
        $meanDiff = array_sum($differences) / $n;
        $varDiff = array_sum(array_map(fn($d) => pow($d - $meanDiff, 2), $differences)) / ($n - 1);
        $stdDiff = sqrt($varDiff);
        $tStatistic = $meanDiff / ($stdDiff / sqrt($n));
        
        // Simplified p-value calculation (for demonstration)
        $pValue = 2 * (1 - $this->tDistribution(abs($tStatistic), $n - 1));
        
        return [
            't_statistic' => $tStatistic,
            'p_value' => $pValue,
            'significant' => $pValue < 0.05,
            'mean_difference' => $meanDiff
        ];
    }
    
    private function tDistribution(float $t, int $df): float
    {
        // Simplified t-distribution CDF
        // In practice, you'd use a proper statistical library
        return 1 - abs($t) / sqrt($df);
    }
    
    public function getComparisonTable(): array
    {
        $table = [];
        
        foreach ($this->models as $name => $metrics) {
            $table[$name] = $metrics;
        }
        
        return $table;
    }
}

// Model Evaluation Examples
class ModelEvaluationExamples
{
    public function demonstrateClassificationMetrics(): void
    {
        echo "Classification Metrics Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Sample classification results
        $trueLabels = ['cat', 'dog', 'cat', 'dog', 'cat', 'dog', 'bird', 'bird', 'cat', 'dog'];
        $predictedLabels = ['cat', 'cat', 'cat', 'dog', 'dog', 'dog', 'bird', 'cat', 'cat', 'dog'];
        $probabilities = [
            ['cat' => 0.9, 'dog' => 0.1, 'bird' => 0.0],
            ['cat' => 0.6, 'dog' => 0.4, 'bird' => 0.0],
            ['cat' => 0.8, 'dog' => 0.2, 'bird' => 0.0],
            ['cat' => 0.1, 'dog' => 0.8, 'bird' => 0.1],
            ['cat' => 0.3, 'dog' => 0.6, 'bird' => 0.1],
            ['cat' => 0.1, 'dog' => 0.9, 'bird' => 0.0],
            ['cat' => 0.0, 'dog' => 0.1, 'bird' => 0.9],
            ['cat' => 0.4, 'dog' => 0.3, 'bird' => 0.3],
            ['cat' => 0.7, 'dog' => 0.2, 'bird' => 0.1],
            ['cat' => 0.2, 'dog' => 0.7, 'bird' => 0.1]
        ];
        
        $metrics = new ClassificationMetrics($trueLabels, $predictedLabels, $probabilities);
        
        echo "True labels: " . implode(', ', $trueLabels) . "\n";
        echo "Predicted: " . implode(', ', $predictedLabels) . "\n\n";
        
        // Confusion Matrix
        echo "Confusion Matrix:\n";
        $confusionMatrix = $metrics->getConfusionMatrix();
        foreach ($confusionMatrix as $true => $row) {
            echo "  $true: ";
            foreach ($row as $pred => $count) {
                echo "$pred($count) ";
            }
            echo "\n";
        }
        
        // Basic metrics
        echo "\nBasic Metrics:\n";
        echo "  Accuracy: " . round($metrics->getAccuracy(), 3) . "\n";
        echo "  Log Loss: " . round($metrics->getLogLoss(), 3) . "\n";
        echo "  ROC AUC (cat): " . round($metrics->getROC_AUC('cat'), 3) . "\n";
        
        // Per-class metrics
        echo "\nPer-Class Metrics:\n";
        $precision = $metrics->getPrecision();
        $recall = $metrics->getRecall();
        $f1 = $metrics->getF1Score();
        
        foreach ($metrics->getClasses() as $class) {
            echo "  $class:\n";
            echo "    Precision: " . round($precision[$class], 3) . "\n";
            echo "    Recall: " . round($recall[$class], 3) . "\n";
            echo "    F1-Score: " . round($f1[$class], 3) . "\n";
        }
        
        // Average metrics
        echo "\nAverage Metrics:\n";
        $macroAvg = $metrics->getMacroAverage();
        $weightedAvg = $metrics->getWeightedAverage();
        
        echo "  Macro Avg:\n";
        echo "    Precision: " . round($macroAvg['precision'], 3) . "\n";
        echo "    Recall: " . round($macroAvg['recall'], 3) . "\n";
        echo "    F1-Score: " . round($macroAvg['f1'], 3) . "\n";
        
        echo "  Weighted Avg:\n";
        echo "    Precision: " . round($weightedAvg['precision'], 3) . "\n";
        echo "    Recall: " . round($weightedAvg['recall'], 3) . "\n";
        echo "    F1-Score: " . round($weightedAvg['f1'], 3) . "\n";
        
        // Classification report
        echo "\nClassification Report:\n";
        $report = $metrics->getClassificationReport();
        foreach ($report as $class => $metrics) {
            if (is_array($metrics)) {
                echo "  $class:\n";
                foreach ($metrics as $metric => $value) {
                    echo "    $metric: $value\n";
                }
            } else {
                echo "  $class: $metrics\n";
            }
        }
    }
    
    public function demonstrateRegressionMetrics(): void
    {
        echo "\nRegression Metrics Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Sample regression results
        $trueValues = [2.5, 3.0, 3.5, 4.0, 4.5, 5.0, 5.5, 6.0, 6.5, 7.0];
        $predictedValues = [2.7, 2.9, 3.6, 4.1, 4.4, 5.1, 5.4, 6.2, 6.3, 6.9];
        
        echo "True values: " . implode(', ', $trueValues) . "\n";
        echo "Predicted: " . implode(', ', $predictedValues) . "\n\n";
        
        $metrics = new RegressionMetrics($trueValues, $predictedValues);
        
        echo "Regression Metrics:\n";
        $allMetrics = $metrics->getMetrics();
        foreach ($allMetrics as $metric => $value) {
            echo "  $metric: $value\n";
        }
        
        echo "\nDetailed Metrics:\n";
        echo "  MAE: " . round($metrics->getMAE(), 4) . "\n";
        echo "  MSE: " . round($metrics->getMSE(), 4) . "\n";
        echo "  RMSE: " . round($metrics->getRMSE(), 4) . "\n";
        echo "  R²: " . round($metrics->getR2(), 4) . "\n";
        echo "  Adjusted R²: " . round($metrics->getAdjustedR2(1), 4) . "\n";
        echo "  MAPE: " . round($metrics->getMAPE(), 2) . "%\n";
    }
    
    public function demonstrateCrossValidation(): void
    {
        echo "\nCross-Validation Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Generate sample data
        $data = [];
        $labels = [];
        
        for ($i = 0; $i < 100; $i++) {
            $data[] = ['feature1' => rand(0, 100), 'feature2' => rand(0, 100)];
            $labels[] = rand(0, 1);
        }
        
        $cv = new CrossValidation($data, $labels, 5);
        
        echo "Total samples: " . count($data) . "\n";
        echo "Number of folds: 5\n\n";
        
        // Regular k-fold
        echo "Regular K-Fold Cross-Validation:\n";
        $folds = $cv->getFolds();
        
        foreach ($folds as $i => $fold) {
            echo "  Fold $i:\n";
            echo "    Train: " . count($fold['train_data']) . " samples\n";
            echo "    Test: " . count($fold['test_data']) . " samples\n";
        }
        
        // Stratified k-fold
        echo "\nStratified K-Fold Cross-Validation:\n";
        $stratifiedFolds = $cv->stratifiedKFolds();
        
        foreach ($stratifiedFolds as $i => $fold) {
            echo "  Fold $i:\n";
            echo "    Train: " . count($fold['train_data']) . " samples\n";
            echo "    Test: " . count($fold['test_data']) . " samples\n";
            
            // Check class distribution
            $trainClass0 = count(array_filter($fold['train_labels'], fn($l) => $l == 0));
            $trainClass1 = count(array_filter($fold['train_labels'], fn($l) => $l == 1));
            $testClass0 = count(array_filter($fold['test_labels'], fn($l) => $l == 0));
            $testClass1 = count(array_filter($fold['test_labels'], fn($l) => $l == 1));
            
            echo "    Train class distribution: 0($trainClass0), 1($trainClass1)\n";
            echo "    Test class distribution: 0($testClass0), 1($testClass1)\n";
        }
        
        // Simulate model evaluation
        echo "\nSimulated Model Evaluation:\n";
        
        $mockModel = new class {
            public function predict(array $data) {
                return array_map(fn() => rand(0, 1), $data);
            }
        };
        
        $evaluation = $cv->evaluate(
            function($trainData, $trainLabels) use ($mockModel) {
                return $mockModel;
            },
            function($trueLabels, $predictedLabels) {
                $correct = 0;
                for ($i = 0; $i < count($trueLabels); $i++) {
                    if ($trueLabels[$i] === $predictedLabels[$i]) {
                        $correct++;
                    }
                }
                return $correct / count($trueLabels);
            }
        );
        
        echo "  Scores: " . implode(', ', array_map(fn($s) => round($s, 3), $evaluation['scores'])) . "\n";
        echo "  Mean: " . round($evaluation['mean'], 3) . "\n";
        echo "  Std: " . round($evaluation['std'], 3) . "\n";
    }
    
    public function demonstrateModelComparison(): void
    {
        echo "\nModel Comparison Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $comparison = new ModelComparison();
        
        // Add model results
        $comparison->addModel('Random Forest', [
            'accuracy' => 0.85,
            'precision' => 0.82,
            'recall' => 0.88,
            'f1' => 0.85,
            'training_time' => 2.5
        ]);
        
        $comparison->addModel('SVM', [
            'accuracy' => 0.83,
            'precision' => 0.80,
            'recall' => 0.86,
            'f1' => 0.83,
            'training_time' => 1.8
        ]);
        
        $comparison->addModel('Logistic Regression', [
            'accuracy' => 0.78,
            'precision' => 0.75,
            'recall' => 0.81,
            'f1' => 0.78,
            'training_time' => 0.5
        ]);
        
        $comparison->addModel('Neural Network', [
            'accuracy' => 0.87,
            'precision' => 0.85,
            'recall' => 0.89,
            'f1' => 0.87,
            'training_time' => 5.2
        ]);
        
        // Compare by different metrics
        echo "Comparison by Accuracy:\n";
        $accuracyComparison = $comparison->compareModels('accuracy');
        foreach ($accuracyComparison as $model => $score) {
            echo "  $model: " . round($score, 3) . "\n";
        }
        
        echo "\nBest model by accuracy: " . $comparison->getBestModel('accuracy') . "\n";
        
        echo "\nComparison by F1-Score:\n";
        $f1Comparison = $comparison->compareModels('f1');
        foreach ($f1Comparison as $model => $score) {
            echo "  $model: " . round($score, 3) . "\n";
        }
        
        echo "\nBest model by F1-Score: " . $comparison->getBestModel('f1') . "\n";
        
        // Statistical test
        echo "\nStatistical Test (Random Forest vs SVM):\n";
        $scores1 = [0.82, 0.86, 0.84, 0.87, 0.85];
        $scores2 = [0.81, 0.84, 0.82, 0.85, 0.83];
        
        $testResult = $comparison->getStatisticalTest('Random Forest', 'SVM', $scores1, $scores2);
        echo "  T-statistic: " . round($testResult['t_statistic'], 3) . "\n";
        echo "  P-value: " . round($testResult['p_value'], 3) . "\n";
        echo "  Significant: " . ($testResult['significant'] ? 'Yes' : 'No') . "\n";
        echo "  Mean difference: " . round($testResult['mean_difference'], 3) . "\n";
        
        // Full comparison table
        echo "\nFull Comparison Table:\n";
        $table = $comparison->getComparisonTable();
        foreach ($table as $model => $metrics) {
            echo "  $model:\n";
            foreach ($metrics as $metric => $value) {
                echo "    $metric: " . round($value, 3) . "\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nModel Evaluation Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Classification Metrics:\n";
        echo "   • Use accuracy for balanced datasets\n";
        echo "   • Use precision/recall for imbalanced datasets\n";
        echo "   • Use F1-score for overall performance\n";
        echo "   • Use ROC-AUC for threshold-independent evaluation\n";
        echo "   • Consider class-specific metrics\n\n";
        
        echo "2. Regression Metrics:\n";
        echo "   • Use MAE for interpretable error\n";
        echo "   • Use MSE for penalizing large errors\n";
        echo "   • Use RMSE for same scale as target\n";
        echo "   • Use R² for explained variance\n";
        echo "   • Use MAPE for percentage error\n\n";
        
        echo "3. Cross-Validation:\n";
        echo "   • Use k-fold for general performance\n";
        echo "   • Use stratified k-fold for imbalanced data\n";
        echo "   • Use leave-one-out for small datasets\n";
        echo "   • Use time series split for temporal data\n";
        echo "   • Report mean and standard deviation\n\n";
        
        echo "4. Model Comparison:\n";
        echo "   • Compare multiple metrics\n";
        echo "   • Use statistical tests for significance\n";
        echo "   • Consider computational cost\n";
        echo "   • Evaluate on multiple datasets\n";
        echo "   • Document comparison methodology\n\n";
        
        echo "5. Reporting:\n";
        echo "   • Report confidence intervals\n";
        echo "   • Include baseline performance\n";
        echo "   • Show confusion matrices\n";
        echo "   • Provide learning curves\n";
        echo "   • Document evaluation protocol";
    }
    
    public function runAllExamples(): void
    {
        echo "Model Evaluation Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateClassificationMetrics();
        $this->demonstrateRegressionMetrics();
        $this->demonstrateCrossValidation();
        $this->demonstrateModelComparison();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runModelEvaluationDemo(): void
{
    $examples = new ModelEvaluationExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runModelEvaluationDemo();
}
?>
