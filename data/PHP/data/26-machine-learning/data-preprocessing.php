<?php
/**
 * Data Preprocessing for Machine Learning
 * 
 * Data cleaning, normalization, feature engineering, and preparation.
 */

// Data Preprocessor
class DataPreprocessor
{
    private array $data = [];
    private array $scalers = [];
    private array $encoders = [];
    private array $features = [];
    
    public function loadData(array $data): void
    {
        $this->data = $data;
        $this->features = array_keys($data[0] ?? []);
        echo "Loaded " . count($data) . " samples with " . count($this->features) . " features\n";
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function getFeatures(): array
    {
        return $this->features;
    }
    
    // Handle missing values
    public function handleMissingValues(string $strategy = 'mean'): void
    {
        echo "Handling missing values with strategy: $strategy\n";
        
        foreach ($this->features as $feature) {
            $values = array_column($this->data, $feature);
            $missingCount = count(array_filter($values, fn($v) => $v === null || $v === '' || $v === 'N/A'));
            
            if ($missingCount > 0) {
                echo "  Feature '$feature': $missingCount missing values\n";
                
                $fillValue = $this->calculateFillValue($values, $strategy);
                
                foreach ($this->data as &$sample) {
                    if ($sample[$feature] === null || $sample[$feature] === '' || $sample[$feature] === 'N/A') {
                        $sample[$feature] = $fillValue;
                    }
                }
            }
        }
        
        echo "Missing values handled\n\n";
    }
    
    private function calculateFillValue(array $values, string $strategy)
    {
        $cleanValues = array_filter($values, fn($v) => $v !== null && $v !== '' && $v !== 'N/A');
        
        if (empty($cleanValues)) {
            return 0;
        }
        
        switch ($strategy) {
            case 'mean':
                return array_sum($cleanValues) / count($cleanValues);
            case 'median':
                sort($cleanValues);
                $count = count($cleanValues);
                $middle = floor($count / 2);
                return $count % 2 === 0 ? 
                    ($cleanValues[$middle - 1] + $cleanValues[$middle]) / 2 : 
                    $cleanValues[$middle];
            case 'mode':
                $frequency = array_count_values($cleanValues);
                arsort($frequency);
                return key($frequency);
            case 'zero':
                return 0;
            default:
                return array_sum($cleanValues) / count($cleanValues);
        }
    }
    
    // Feature scaling
    public function scaleFeatures(string $method = 'standard'): void
    {
        echo "Scaling features with method: $method\n";
        
        foreach ($this->features as $feature) {
            $values = array_column($this->data, $feature);
            
            // Check if feature is numeric
            if ($this->isNumericFeature($values)) {
                echo "  Scaling feature: $feature\n";
                
                switch ($method) {
                    case 'standard':
                        $this->standardize($feature);
                        break;
                    case 'minmax':
                        $this->minMaxScale($feature);
                        break;
                    case 'robust':
                        $this->robustScale($feature);
                        break;
                }
            }
        }
        
        echo "Feature scaling completed\n\n";
    }
    
    private function isNumericFeature(array $values): bool
    {
        foreach ($values as $value) {
            if (!is_numeric($value) && $value !== null && $value !== '') {
                return false;
            }
        }
        return true;
    }
    
    private function standardize(string $feature): void
    {
        $values = array_column($this->data, $feature);
        $mean = array_sum($values) / count($values);
        
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        $stdDev = sqrt($variance / count($values));
        
        $this->scalers[$feature] = [
            'method' => 'standard',
            'mean' => $mean,
            'std_dev' => $stdDev
        ];
        
        foreach ($this->data as &$sample) {
            if ($stdDev != 0) {
                $sample[$feature] = ($sample[$feature] - $mean) / $stdDev;
            }
        }
    }
    
    private function minMaxScale(string $feature): void
    {
        $values = array_column($this->data, $feature);
        $min = min($values);
        $max = max($values);
        
        $this->scalers[$feature] = [
            'method' => 'minmax',
            'min' => $min,
            'max' => $max
        ];
        
        foreach ($this->data as &$sample) {
            if ($max != $min) {
                $sample[$feature] = ($sample[$feature] - $min) / ($max - $min);
            } else {
                $sample[$feature] = 0;
            }
        }
    }
    
    private function robustScale(string $feature): void
    {
        $values = array_column($this->data, $feature);
        sort($values);
        $count = count($values);
        $median = $count % 2 === 0 ? 
            ($values[$count / 2 - 1] + $values[$count / 2]) / 2 : 
            $values[floor($count / 2)];
        
        $q1Index = floor($count * 0.25);
        $q3Index = floor($count * 0.75);
        $q1 = $values[$q1Index];
        $q3 = $values[$q3Index];
        $iqr = $q3 - $q1;
        
        $this->scalers[$feature] = [
            'method' => 'robust',
            'median' => $median,
            'iqr' => $iqr
        ];
        
        foreach ($this->data as &$sample) {
            if ($iqr != 0) {
                $sample[$feature] = ($sample[$feature] - $median) / $iqr;
            } else {
                $sample[$feature] = 0;
            }
        }
    }
    
    // Encode categorical features
    public function encodeCategoricalFeatures(string $method = 'onehot'): void
    {
        echo "Encoding categorical features with method: $method\n";
        
        foreach ($this->features as $feature) {
            $values = array_column($this->data, $feature);
            
            if (!$this->isNumericFeature($values)) {
                echo "  Encoding feature: $feature\n";
                
                switch ($method) {
                    case 'label':
                        $this->labelEncode($feature);
                        break;
                    case 'onehot':
                        $this->oneHotEncode($feature);
                        break;
                    case 'ordinal':
                        $this->ordinalEncode($feature);
                        break;
                }
            }
        }
        
        echo "Categorical encoding completed\n\n";
    }
    
    private function labelEncode(string $feature): void
    {
        $values = array_column($this->data, $feature);
        $uniqueValues = array_unique($values);
        
        $this->encoders[$feature] = [
            'method' => 'label',
            'mapping' => array_flip($uniqueValues)
        ];
        
        foreach ($this->data as &$sample) {
            $sample[$feature] = $this->encoders[$feature]['mapping'][$sample[$feature]];
        }
    }
    
    private function oneHotEncode(string $feature): void
    {
        $values = array_column($this->data, $feature);
        $uniqueValues = array_unique($values);
        
        $this->encoders[$feature] = [
            'method' => 'onehot',
            'categories' => $uniqueValues
        ];
        
        // Create new columns for each category
        foreach ($this->data as &$sample) {
            $originalValue = $sample[$feature];
            
            foreach ($uniqueValues as $category) {
                $newFeatureName = "{$feature}_{$category}";
                $sample[$newFeatureName] = ($originalValue === $category) ? 1 : 0;
            }
            
            // Remove original categorical column
            unset($sample[$feature]);
        }
        
        // Update features list
        $this->features = array_keys($this->data[0]);
    }
    
    private function ordinalEncode(string $feature): void
    {
        $values = array_column($this->data, $feature);
        $uniqueValues = array_unique($values);
        sort($uniqueValues);
        
        $this->encoders[$feature] = [
            'method' => 'ordinal',
            'mapping' => array_flip($uniqueValues)
        ];
        
        foreach ($this->data as &$sample) {
            $sample[$feature] = $this->encoders[$feature]['mapping'][$sample[$feature]];
        }
    }
    
    // Feature engineering
    public function createPolynomialFeatures(int $degree = 2): void
    {
        echo "Creating polynomial features (degree: $degree)\n";
        
        $numericFeatures = [];
        foreach ($this->features as $feature) {
            $values = array_column($this->data, $feature);
            if ($this->isNumericFeature($values)) {
                $numericFeatures[] = $feature;
            }
        }
        
        foreach ($this->data as &$sample) {
            foreach ($numericFeatures as $feature1) {
                for ($d = 2; $d <= $degree; $d++) {
                    $newFeatureName = "{$feature}_pow_{$d}";
                    $sample[$newFeatureName] = pow($sample[$feature], $d);
                }
                
                foreach ($numericFeatures as $feature2) {
                    if ($feature1 <= $feature2) {
                        $newFeatureName = "{$feature1}_x_{$feature2}";
                        $sample[$newFeatureName] = $sample[$feature1] * $sample[$feature2];
                    }
                }
            }
        }
        
        $this->features = array_keys($this->data[0]);
        echo "Created " . (count($this->features) - count($numericFeatures)) . " new features\n\n";
    }
    
    public function createInteractionFeatures(): void
    {
        echo "Creating interaction features\n";
        
        $numericFeatures = [];
        foreach ($this->features as $feature) {
            $values = array_column($this->data, $feature);
            if ($this->isNumericFeature($values)) {
                $numericFeatures[] = $feature;
            }
        }
        
        foreach ($this->data as &$sample) {
            foreach ($numericFeatures as $i => $feature1) {
                foreach ($numericFeatures as $j => $feature2) {
                    if ($i < $j) {
                        $newFeatureName = "{$feature1}_x_{$feature2}";
                        $sample[$newFeatureName] = $sample[$feature1] * $sample[$feature2];
                    }
                }
            }
        }
        
        $this->features = array_keys($this->data[0]);
        echo "Created " . (count($numericFeatures) * (count($numericFeatures) - 1) / 2) . " interaction features\n\n";
    }
    
    // Feature selection
    public function selectFeaturesByCorrelation(float $threshold = 0.95): void
    {
        echo "Selecting features by correlation (threshold: $threshold)\n";
        
        $numericFeatures = [];
        foreach ($this->features as $feature) {
            $values = array_column($this->data, $feature);
            if ($this->isNumericFeature($values)) {
                $numericFeatures[] = $feature;
            }
        }
        
        $correlationMatrix = $this->calculateCorrelationMatrix($numericFeatures);
        $featuresToRemove = [];
        
        for ($i = 0; $i < count($numericFeatures); $i++) {
            for ($j = $i + 1; $j < count($numericFeatures); $j++) {
                $feature1 = $numericFeatures[$i];
                $feature2 = $numericFeatures[$j];
                $correlation = abs($correlationMatrix[$feature1][$feature2]);
                
                if ($correlation > $threshold) {
                    $featuresToRemove[] = $feature2;
                    echo "  Removing '{$feature2}' (correlation with '{$feature1}': " . round($correlation, 3) . ")\n";
                }
            }
        }
        
        $featuresToRemove = array_unique($featuresToRemove);
        
        foreach ($this->data as &$sample) {
            foreach ($featuresToRemove as $feature) {
                unset($sample[$feature]);
            }
        }
        
        $this->features = array_keys($this->data[0]);
        echo "Removed " . count($featuresToRemove) . " highly correlated features\n\n";
    }
    
    private function calculateCorrelationMatrix(array $features): array
    {
        $matrix = [];
        
        foreach ($features as $feature1) {
            $matrix[$feature1] = [];
            $values1 = array_column($this->data, $feature1);
            $mean1 = array_sum($values1) / count($values1);
            
            foreach ($features as $feature2) {
                $values2 = array_column($this->data, $feature2);
                $mean2 = array_sum($values2) / count($values2);
                
                $correlation = $this->pearsonCorrelation($values1, $values2, $mean1, $mean2);
                $matrix[$feature1][$feature2] = $correlation;
            }
        }
        
        return $matrix;
    }
    
    private function pearsonCorrelation(array $x, array $y, float $meanX, float $meanY): float
    {
        $n = count($x);
        $numerator = 0;
        $denominatorX = 0;
        $denominatorY = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $dx = $x[$i] - $meanX;
            $dy = $y[$i] - $meanY;
            $numerator += $dx * $dy;
            $denominatorX += $dx * $dx;
            $denominatorY += $dy * $dy;
        }
        
        $denominator = sqrt($denominatorX * $denominatorY);
        return $denominator == 0 ? 0 : $numerator / $denominator;
    }
    
    // Train-test split
    public function trainTestSplit(float $testSize = 0.2): array
    {
        echo "Splitting data (test size: " . ($testSize * 100) . "%)\n";
        
        $shuffledData = $this->data;
        shuffle($shuffledData);
        
        $testCount = (int) (count($shuffledData) * $testSize);
        $testData = array_slice($shuffledData, 0, $testCount);
        $trainData = array_slice($shuffledData, $testCount);
        
        echo "Training set: " . count($trainData) . " samples\n";
        echo "Test set: " . count($testData) . " samples\n\n";
        
        return [
            'train' => $trainData,
            'test' => $testData
        ];
    }
    
    // Apply transformations to new data
    public function transform(array $newData): array
    {
        $transformedData = $newData;
        
        // Apply scalers
        foreach ($this->scalers as $feature => $scaler) {
            switch ($scaler['method']) {
                case 'standard':
                    foreach ($transformedData as &$sample) {
                        if ($scaler['std_dev'] != 0) {
                            $sample[$feature] = ($sample[$feature] - $scaler['mean']) / $scaler['std_dev'];
                        }
                    }
                    break;
                case 'minmax':
                    foreach ($transformedData as &$sample) {
                        if ($scaler['max'] != $scaler['min']) {
                            $sample[$feature] = ($sample[$feature] - $scaler['min']) / ($scaler['max'] - $scaler['min']);
                        }
                    }
                    break;
                case 'robust':
                    foreach ($transformedData as &$sample) {
                        if ($scaler['iqr'] != 0) {
                            $sample[$feature] = ($sample[$feature] - $scaler['median']) / $scaler['iqr'];
                        }
                    }
                    break;
            }
        }
        
        // Apply encoders
        foreach ($this->encoders as $feature => $encoder) {
            switch ($encoder['method']) {
                case 'label':
                case 'ordinal':
                    foreach ($transformedData as &$sample) {
                        if (isset($encoder['mapping'][$sample[$feature]])) {
                            $sample[$feature] = $encoder['mapping'][$sample[$feature]];
                        }
                    }
                    break;
                case 'onehot':
                    foreach ($transformedData as &$sample) {
                        $originalValue = $sample[$feature] ?? '';
                        
                        foreach ($encoder['categories'] as $category) {
                            $newFeatureName = "{$feature}_{$category}";
                            $sample[$newFeatureName] = ($originalValue === $category) ? 1 : 0;
                        }
                        
                        unset($sample[$feature]);
                    }
                    break;
            }
        }
        
        return $transformedData;
    }
    
    // Get preprocessing info
    public function getScalers(): array
    {
        return $this->scalers;
    }
    
    public function getEncoders(): array
    {
        return $this->encoders;
    }
    
    public function getDataSummary(): array
    {
        $summary = [
            'total_samples' => count($this->data),
            'total_features' => count($this->features),
            'numeric_features' => 0,
            'categorical_features' => 0,
            'missing_values' => 0
        ];
        
        foreach ($this->features as $feature) {
            $values = array_column($this->data, $feature);
            
            if ($this->isNumericFeature($values)) {
                $summary['numeric_features']++;
            } else {
                $summary['categorical_features']++;
            }
            
            $summary['missing_values'] += count(array_filter($values, fn($v) => $v === null || $v === ''));
        }
        
        return $summary;
    }
}

// Feature Engineering Tools
class FeatureEngineering
{
    public static function createBinnedFeature(array $values, array $bins): array
    {
        $binned = [];
        
        foreach ($values as $value) {
            $binIndex = 0;
            
            for ($i = 0; $i < count($bins) - 1; $i++) {
                if ($value >= $bins[$i] && $value < $bins[$i + 1]) {
                    $binIndex = $i;
                    break;
                }
            }
            
            if ($value >= $bins[count($bins) - 1]) {
                $binIndex = count($bins) - 1;
            }
            
            $binned[] = $binIndex;
        }
        
        return $binned;
    }
    
    public static function createLogFeature(array $values): array
    {
        return array_map(function($value) {
            return $value > 0 ? log($value) : 0;
        }, $values);
    }
    
    public static function createSqrtFeature(array $values): array
    {
        return array_map(function($value) {
            return $value >= 0 ? sqrt($value) : 0;
        }, $values);
    }
    
    public static function createReciprocalFeature(array $values): array
    {
        return array_map(function($value) {
            return $value != 0 ? 1 / $value : 0;
        }, $values);
    }
    
    public static function detectOutliers(array $values, float $threshold = 1.5): array
    {
        sort($values);
        $count = count($values);
        
        $q1 = $values[floor($count * 0.25)];
        $q3 = $values[floor($count * 0.75)];
        $iqr = $q3 - $q1;
        
        $lowerBound = $q1 - $threshold * $iqr;
        $upperBound = $q3 + $threshold * $iqr;
        
        $outliers = [];
        foreach ($values as $index => $value) {
            if ($value < $lowerBound || $value > $upperBound) {
                $outliers[] = $index;
            }
        }
        
        return $outliers;
    }
    
    public static function calculateVIF(array $data, string $targetFeature): array
    {
        // Simplified VIF calculation
        $features = array_keys($data[0]);
        $vifValues = [];
        
        foreach ($features as $feature) {
            if ($feature !== $targetFeature) {
                // Calculate R² between feature and target
                $correlation = self::calculateCorrelation(
                    array_column($data, $feature),
                    array_column($data, $targetFeature)
                );
                $rSquared = $correlation * $correlation;
                $vifValues[$feature] = $rSquared == 1 ? INF : 1 / (1 - $rSquared);
            }
        }
        
        return $vifValues;
    }
    
    private static function calculateCorrelation(array $x, array $y): float
    {
        $n = count($x);
        $meanX = array_sum($x) / $n;
        $meanY = array_sum($y) / $n;
        
        $numerator = 0;
        $denominatorX = 0;
        $denominatorY = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $dx = $x[$i] - $meanX;
            $dy = $y[$i] - $meanY;
            $numerator += $dx * $dy;
            $denominatorX += $dx * $dx;
            $denominatorY += $dy * $dy;
        }
        
        $denominator = sqrt($denominatorX * $denominatorY);
        return $denominator == 0 ? 0 : $numerator / $denominator;
    }
}

// Data Preprocessing Examples
class DataPreprocessingExamples
{
    public function demonstrateBasicPreprocessing(): void
    {
        echo "Basic Data Preprocessing Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // Sample dataset
        $data = [
            ['age' => 25, 'income' => 50000, 'gender' => 'male', 'city' => 'New York', 'education' => 'Bachelor'],
            ['age' => 30, 'income' => 60000, 'gender' => 'female', 'city' => 'Los Angeles', 'education' => 'Master'],
            ['age' => null, 'income' => 55000, 'gender' => 'male', 'city' => 'Chicago', 'education' => 'Bachelor'],
            ['age' => 35, 'income' => null, 'gender' => 'female', 'city' => 'New York', 'education' => 'PhD'],
            ['age' => 28, 'income' => 52000, 'gender' => 'male', 'city' => 'Los Angeles', 'education' => 'Master'],
            ['age' => 32, 'income' => 70000, 'gender' => null, 'city' => 'Chicago', 'education' => 'Bachelor'],
            ['age' => 27, 'income' => 48000, 'gender' => 'female', 'city' => 'New York', 'education' => null],
            ['age' => 40, 'income' => 80000, 'gender' => 'male', 'city' => 'Los Angeles', 'education' => 'PhD']
        ];
        
        $preprocessor = new DataPreprocessor();
        $preprocessor->loadData($data);
        
        echo "Original data summary:\n";
        $summary = $preprocessor->getDataSummary();
        foreach ($summary as $key => $value) {
            echo "  $key: $value\n";
        }
        echo "\n";
        
        // Handle missing values
        $preprocessor->handleMissingValues('mean');
        
        // Scale numeric features
        $preprocessor->scaleFeatures('standard');
        
        // Encode categorical features
        $preprocessor->encodeCategoricalFeatures('onehot');
        
        echo "Processed data summary:\n";
        $summary = $preprocessor->getDataSummary();
        foreach ($summary as $key => $value) {
            echo "  $key: $value\n";
        }
        
        echo "\nSample of processed data:\n";
        $processedData = $preprocessor->getData();
        for ($i = 0; $i < min(3, count($processedData)); $i++) {
            echo "Sample $i:\n";
            foreach ($processedData[$i] as $feature => $value) {
                echo "  $feature: " . round($value, 3) . "\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateFeatureEngineering(): void
    {
        echo "\nFeature Engineering Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Sample dataset
        $data = [
            ['height' => 170, 'weight' => 70, 'age' => 25, 'income' => 50000],
            ['height' => 165, 'weight' => 65, 'age' => 30, 'income' => 60000],
            ['height' => 180, 'weight' => 80, 'age' => 35, 'income' => 70000],
            ['height' => 175, 'weight' => 75, 'age' => 28, 'income' => 55000],
            ['height' => 160, 'weight' => 60, 'age' => 32, 'income' => 65000]
        ];
        
        $preprocessor = new DataPreprocessor();
        $preprocessor->loadData($data);
        
        echo "Original features: " . implode(', ', $preprocessor->getFeatures()) . "\n\n";
        
        // Create polynomial features
        $preprocessor->createPolynomialFeatures(2);
        
        echo "After polynomial features: " . count($preprocessor->getFeatures()) . " features\n";
        echo "Sample of new features:\n";
        $processedData = $preprocessor->getData();
        $newFeatures = array_slice($preprocessor->getFeatures(), 4, 5);
        
        foreach ($newFeatures as $feature) {
            echo "  $feature: " . $processedData[0][$feature] . "\n";
        }
        
        // Create interaction features
        $preprocessor->createInteractionFeatures();
        
        echo "\nAfter interaction features: " . count($preprocessor->getFeatures()) . " features\n";
        
        // Feature selection by correlation
        $preprocessor->selectFeaturesByCorrelation(0.9);
        
        echo "\nFinal features: " . count($preprocessor->getFeatures()) . " features\n";
    }
    
    public function demonstrateAdvancedTechniques(): void
    {
        echo "\nAdvanced Preprocessing Techniques Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        // Sample data with outliers
        $data = [
            ['value' => 10, 'category' => 'A'],
            ['value' => 15, 'category' => 'B'],
            ['value' => 20, 'category' => 'A'],
            ['value' => 25, 'category' => 'B'],
            ['value' => 30, 'category' => 'A'],
            ['value' => 35, 'category' => 'B'],
            ['value' => 100, 'category' => 'A'], // Outlier
            ['value' => 40, 'category' => 'B'],
            ['value' => 45, 'category' => 'A'],
            ['value' => 50, 'category' => 'B']
        ];
        
        $preprocessor = new DataPreprocessor();
        $preprocessor->loadData($data);
        
        // Detect outliers
        $values = array_column($data, 'value');
        $outliers = FeatureEngineering::detectOutliers($values);
        
        echo "Outlier detection:\n";
        echo "  Values: " . implode(', ', $values) . "\n";
        echo "  Outlier indices: " . implode(', ', $outliers) . "\n";
        echo "  Outlier values: " . implode(', ', array_map(fn($i) => $values[$i], $outliers)) . "\n\n";
        
        // Create binned features
        $bins = [0, 20, 40, 60, 80, 100];
        $binnedValues = FeatureEngineering::createBinnedFeature($values, $bins);
        
        echo "Binned features:\n";
        echo "  Bins: " . implode(', ', $bins) . "\n";
        echo "  Binned values: " . implode(', ', $binnedValues) . "\n\n";
        
        // Transform features
        $logValues = FeatureEngineering::createLogFeature($values);
        $sqrtValues = FeatureEngineering::createSqrtFeature($values);
        
        echo "Feature transformations:\n";
        echo "  Original: " . implode(', ', $values) . "\n";
        echo "  Log: " . implode(', ', array_map(fn($v) => round($v, 2), $logValues)) . "\n";
        echo "  Sqrt: " . implode(', ', array_map(fn($v) => round($v, 2), $sqrtValues)) . "\n\n";
        
        // VIF calculation
        $vifValues = FeatureEngineering::calculateVIF($data, 'value');
        
        echo "VIF (Variance Inflation Factor):\n";
        foreach ($vifValues as $feature => $vif) {
            echo "  $feature: " . round($vif, 2) . "\n";
        }
    }
    
    public function demonstrateTrainTestSplit(): void
    {
        echo "\nTrain-Test Split Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Generate sample data
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $data[] = [
                'feature1' => rand(0, 100),
                'feature2' => rand(0, 100),
                'feature3' => rand(0, 100),
                'target' => rand(0, 1)
            ];
        }
        
        $preprocessor = new DataPreprocessor();
        $preprocessor->loadData($data);
        
        echo "Total samples: " . count($data) . "\n";
        
        // Different split ratios
        $splits = [0.2, 0.3, 0.25];
        
        foreach ($splits as $testSize) {
            echo "\nTest size: " . ($testSize * 100) . "%\n";
            $split = $preprocessor->trainTestSplit($testSize);
            
            echo "  Training: " . count($split['train']) . " samples\n";
            echo "  Testing: " . count($split['test']) . " samples\n";
        }
        
        // Transform new data
        echo "\nTransforming new data:\n";
        $newData = [
            ['feature1' => 50, 'feature2' => 75, 'feature3' => 25, 'target' => 1],
            ['feature1' => 30, 'feature2' => 45, 'feature3' => 60, 'target' => 0]
        ];
        
        // First, fit on original data
        $preprocessor->scaleFeatures('standard');
        
        // Then transform new data
        $transformedNewData = $preprocessor->transform($newData);
        
        echo "New data transformed:\n";
        foreach ($transformedNewData as $i => $sample) {
            echo "  Sample $i:\n";
            foreach ($sample as $feature => $value) {
                echo "    $feature: " . round($value, 3) . "\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nData Preprocessing Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. Data Cleaning:\n";
        echo "   • Handle missing values appropriately\n";
        echo "   • Detect and handle outliers\n";
        echo "   • Remove duplicate records\n";
        echo "   • Validate data types and ranges\n";
        echo "   • Check for data consistency\n\n";
        
        echo "2. Feature Scaling:\n";
        echo "   • Use standardization for normally distributed data\n";
        echo "   • Use min-max scaling for bounded features\n";
        echo "   • Use robust scaling for data with outliers\n";
        echo "   • Apply same scaling to train and test data\n";
        echo "   • Consider feature-specific scaling\n\n";
        
        echo "3. Categorical Encoding:\n";
        echo "   • Use one-hot encoding for nominal data\n";
        echo "   • Use label encoding for ordinal data\n";
        echo "   • Handle unseen categories in test data\n";
        echo "   • Consider dimensionality reduction\n";
        echo "   • Document encoding schemes\n\n";
        
        echo "4. Feature Engineering:\n";
        echo "   • Create domain-specific features\n";
        echo "   • Use polynomial features for non-linear relationships\n";
        echo "   • Create interaction features\n";
        echo "   • Apply transformations (log, sqrt, etc.)\n";
        echo "   • Remove redundant features\n\n";
        
        echo "5. Data Splitting:\n";
        echo "   • Use appropriate train-test split ratio\n";
        echo "   • Consider stratified sampling\n";
        echo "   • Keep test data separate\n";
        echo "   • Use cross-validation for model evaluation\n";
        echo "   • Document data splits";
    }
    
    public function runAllExamples(): void
    {
        echo "Data Preprocessing Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateBasicPreprocessing();
        $this->demonstrateFeatureEngineering();
        $this->demonstrateAdvancedTechniques();
        $this->demonstrateTrainTestSplit();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runDataPreprocessingDemo(): void
{
    $examples = new DataPreprocessingExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runDataPreprocessingDemo();
}
?>
