<?php
/**
 * Sensor Data Processing in PHP
 * 
 * Processing, filtering, and analyzing sensor data.
 */

// Sensor Data Processor
class SensorDataProcessor
{
    private array $dataBuffer = [];
    private array $filters = [];
    private array $processors = [];
    private int $maxBufferSize = 1000;
    
    public function __construct()
    {
        $this->initializeFilters();
        $this->initializeProcessors();
    }
    
    private function initializeFilters(): void
    {
        $this->filters = [
            'outlier' => new OutlierFilter(),
            'noise' => new NoiseFilter(),
            'smoothing' => new SmoothingFilter(),
            'calibration' => new CalibrationFilter(),
            'range' => new RangeFilter()
        ];
    }
    
    private function initializeProcessors(): void
    {
        $this->processors = [
            'aggregator' => new DataAggregator(),
            'analyzer' => DataAnalyzer::class,
            'normalizer' => new DataNormalizer(),
            'compressor' => new DataCompressor(),
            'validator' => new DataValidator()
        ];
    }
    
    public function addData(array $sensorData): void
    {
        $this->dataBuffer[] = $sensorData;
        
        // Keep buffer size manageable
        if (count($this->dataBuffer) > $this->maxBufferSize) {
            array_shift($this->dataBuffer);
        }
    }
    
    public function processData(array $data, array $processingSteps = []): array
    {
        $processedData = $data;
        
        foreach ($processingSteps as $step) {
            $parts = explode(':', $step);
            $filterType = $parts[0];
            $params = $parts[1] ?? null;
            
            if (isset($this->filters[$filterType])) {
                $processedData = $this->filters[$filterType]->filter($processedData, $params);
            } elseif (isset($this->processors[$filterType])) {
                if ($filterType === 'analyzer') {
                    $analyzer = new $this->processors[$filterType]();
                    $processedData = $analyzer->analyze($processedData);
                } else {
                    $processedData = $this->processors[$filterType]->process($processedData, $params);
                }
            }
        }
        
        return $processedData;
    }
    
    public function processBuffer(array $processingSteps = []): array
    {
        $processedBuffer = [];
        
        foreach ($this->dataBuffer as $data) {
            $processedBuffer[] = $this->processData($data, $processingSteps);
        }
        
        return $processedBuffer;
    }
    
    public function getBuffer(): array
    {
        return $this->dataBuffer;
    }
    
    public function clearBuffer(): void
    {
        $this->dataBuffer = [];
    }
    
    public function getStatistics(): array
    {
        if (empty($this->dataBuffer)) {
            return [];
        }
        
        $stats = [];
        
        // Group by device type
        $groupedData = [];
        foreach ($this->dataBuffer as $data) {
            $deviceType = $data['device_type'] ?? 'unknown';
            $groupedData[$deviceType][] = $data;
        }
        
        foreach ($groupedData as $deviceType => $dataGroup) {
            $stats[$deviceType] = $this->calculateDeviceStats($dataGroup);
        }
        
        $stats['total_readings'] = count($this->dataBuffer);
        $stats['buffer_size'] = count($this->dataBuffer);
        $stats['max_buffer_size'] = $this->maxBufferSize;
        
        return $stats;
    }
    
    private function calculateDeviceStats(array $dataGroup): array
    {
        $stats = [
            'count' => count($dataGroup),
            'first_timestamp' => $dataGroup[0]['timestamp'] ?? 0,
            'last_timestamp' => end($dataGroup)['timestamp'] ?? 0,
            'time_span' => 0
        ];
        
        if ($stats['last_timestamp'] > $stats['first_timestamp']) {
            $stats['time_span'] = $stats['last_timestamp'] - $stats['first_timestamp'];
        }
        
        // Device-specific statistics
        $deviceType = $dataGroup[0]['device_type'] ?? 'unknown';
        
        switch ($deviceType) {
            case 'temperature_sensor':
                $stats['temperature_stats'] = $this->calculateTemperatureStats($dataGroup);
                break;
            case 'motion_sensor':
                $stats['motion_stats'] = $this->calculateMotionStats($dataGroup);
                break;
            case 'smart_light':
                $stats['light_stats'] = $this->calculateLightStats($dataGroup);
                break;
        }
        
        return $stats;
    }
    
    private function calculateTemperatureStats(array $dataGroup): array
    {
        $temperatures = array_column($dataGroup, 'temperature');
        
        if (empty($temperatures)) {
            return [];
        }
        
        return [
            'min' => min($temperatures),
            'max' => max($temperatures),
            'avg' => array_sum($temperatures) / count($temperatures),
            'latest' => end($temperatures),
            'trend' => $this->calculateTrend($temperatures)
        ];
    }
    
    private function calculateMotionStats(array $dataGroup): array
    {
        $detections = array_column($dataGroup, 'motion_detected');
        
        if (empty($detections)) {
            return [];
        }
        
        $trueDetections = array_filter($detections, fn($d) => $d);
        
        return [
            'total_detections' => count($trueDetections),
            'detection_rate' => count($trueDetections) / count($detections),
            'latest_detection' => end($detections),
            'sensitivity' => $dataGroup[0]['sensitivity'] ?? 5
        ];
    }
    
    private function calculateLightStats(array $dataGroup): array
    {
        $brightnesses = array_column($dataGroup, 'brightness');
        $isOnStates = array_column($dataGroup, 'is_on');
        
        if (empty($brightnesses)) {
            return [];
        }
        
        $onCount = count(array_filter($isOnStates, fn($s) => $s));
        
        return [
            'avg_brightness' => array_sum($brightnesses) / count($brightnesses),
            'on_percentage' => ($onCount / count($isOnStates)) * 100,
            'latest_state' => end($isOnStates),
            'latest_brightness' => end($brightnesses)
        ];
    }
    
    private function calculateTrend(array $values): string
    {
        if (count($values) < 2) {
            return 'stable';
        }
        
        $first = $values[0];
        $last = end($values);
        
        $change = ($last - $first) / $first;
        
        if ($change > 0.05) {
            return 'increasing';
        } elseif ($change < -0.05) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }
    
    public function exportData(string $format = 'json'): string
    {
        switch (strtolower($format)) {
            case 'json':
                return json_encode($this->dataBuffer, JSON_PRETTY_PRINT);
            case 'csv':
                return $this->exportToCSV();
            case 'xml':
                return $this->exportToXML();
            default:
                return json_encode($this->dataBuffer);
        }
    }
    
    private function exportToCSV(): string
    {
        $csv = "device_id,device_type,timestamp,status\n";
        
        foreach ($this->dataBuffer as $data) {
            $csv .= "{$data['device_id']},{$data['device_type']},{$data['timestamp']},{$data['status']}\n";
        }
        
        return $csv;
    }
    
    private function exportToXML(): string
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<sensor_data>\n";
        
        foreach ($this->dataBuffer as $data) {
            $xml .= "  <reading>\n";
            foreach ($data as $key => $value) {
                $xml .= "    <$key>" . htmlspecialchars($value) . "</$key>\n";
            }
            $xml .= "  </reading>\n";
        }
        
        $xml .= "</sensor_data>";
        return $xml;
    }
}

// Data Filter Interface
interface DataFilter
{
    public function filter(array $data, array $params = null): array;
}

// Outlier Filter
class OutlierFilter implements DataFilter
{
    private float $threshold;
    
    public function __construct(float $threshold = 2.0)
    {
        $this->threshold = $threshold;
    }
    
    public function filter(array $data, array $params = null): array
    {
        if (!isset($data['value']) || !is_numeric($data['value'])) {
            return $data;
        }
        
        $value = $data['value'];
        $deviceType = $data['device_type'] ?? 'unknown';
        
        // Get historical data for comparison
        $historicalValues = $this->getHistoricalValues($deviceType);
        
        if (count($historicalValues) < 10) {
            return $data; // Not enough data for outlier detection
        }
        
        $mean = array_sum($historicalValues) / count($historicalValues);
        $stdDev = $this->calculateStandardDeviation($historicalValues, $mean);
        
        $zScore = ($value - $mean) / $stdDev;
        
        if (abs($zScore) > $this->threshold) {
            $data['outlier'] = true;
            $data['z_score'] = $zScore;
            $data['filtered_value'] = $mean; // Replace with mean
        } else {
            $data['outlier'] = false;
            $data['z_score'] = $zScore;
            $data['filtered_value'] = $value;
        }
        
        return $data;
    }
    
    private function getHistoricalValues(string $deviceType): array
    {
        // Simulate historical values
        $values = [];
        
        switch ($deviceType) {
            case 'temperature_sensor':
                for ($i = 0; $i < 20; $i++) {
                    $values[] = 20 + (rand(-5, 5) / 2); // 17.5 to 22.5
                }
                break;
            case 'motion_sensor':
                for ($i = 0; $i < 20; $i++) {
                    $values[] = rand(0, 1);
                }
                break;
            default:
                for ($i = 0; $i < 20; $i++) {
                    $values[] = rand(0, 100);
                }
        }
        
        return $values;
    }
    
    private function calculateStandardDeviation(array $values, float $mean): float
    {
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        
        return sqrt($variance / count($values));
    }
}

// Noise Filter
class NoiseFilter implements DataFilter
{
    private int $windowSize;
    
    public function __construct(int $windowSize = 5)
    {
        $this->windowSize = $windowSize;
    }
    
    public function filter(array $data, array $params = null): array
    {
        if (!isset($data['value']) || !is_numeric($data['value'])) {
            return $data;
        }
        
        $value = $data['value'];
        $deviceType = $data['device_type'] ?? 'unknown';
        
        // Get recent values for moving average
        $recentValues = $this->getRecentValues($deviceType, $this->windowSize);
        
        if (count($recentValues) < 2) {
            $data['filtered_value'] = $value;
            return $data;
        }
        
        // Apply moving average filter
        $movingAverage = array_sum($recentValues) / count($recentValues);
        
        // Apply exponential smoothing
        $alpha = 0.3;
        $smoothedValue = $alpha * $value + (1 - $alpha) * $movingAverage;
        
        $data['original_value'] = $value;
        $data['filtered_value'] = $smoothedValue;
        $data['noise_level'] = abs($value - $smoothedValue);
        
        return $data;
    }
    
    private function getRecentValues(string $deviceType, int $count): array
    {
        // Simulate recent values
        $values = [];
        
        for ($i = 0; $i < $count; $i++) {
            switch ($deviceType) {
                case 'temperature_sensor':
                    $values[] = 20 + (rand(-2, 2) / 1);
                    break;
                default:
                    $values[] = rand(0, 100);
            }
        }
        
        return $values;
    }
}

// Smoothing Filter
class SmoothingFilter implements DataFilter
{
    private string $method;
    private int $windowSize;
    
    public function __construct(string $method = 'moving_average', int $windowSize = 3)
    {
        $this->method = $method;
        $this->windowSize = $windowSize;
    }
    
    public function filter(array $data, array $params = null): array
    {
        if (!isset($data['value']) || !is_numeric($data['value'])) {
            return $data;
        }
        
        $value = $data['value'];
        $deviceType = $data['device_type'] ?? 'unknown';
        
        $recentValues = $this->getRecentValues($deviceType, $this->windowSize);
        $recentValues[] = $value;
        
        switch ($this->method) {
            case 'moving_average':
                $smoothedValue = array_sum($recentValues) / count($recentValues);
                break;
                
            case 'median':
                sort($recentValues);
                $middle = floor(count($recentValues) / 2);
                $smoothedValue = count($recentValues) % 2 === 0 ?
                    ($recentValues[$middle - 1] + $recentValues[$middle]) / 2 :
                    $recentValues[$middle];
                break;
                
            case 'exponential':
                $alpha = 0.3;
                $smoothedValue = $alpha * $value;
                for ($i = 1; $i < count($recentValues) - 1; $i++) {
                    $smoothedValue += (1 - $alpha) * $alpha * $recentValues[$i];
                }
                $smoothedValue += (1 - $alpha) * $recentValues[0];
                break;
                
            default:
                $smoothedValue = $value;
        }
        
        $data['original_value'] = $value;
        $data['smoothed_value'] = $smoothedValue;
        $data['smoothing_method'] = $this->method;
        
        return $data;
    }
    
    private function getRecentValues(string $deviceType, int $count): array
    {
        $values = [];
        
        for ($i = 0; $i < $count; $i++) {
            switch ($deviceType) {
                case 'temperature_sensor':
                    $values[] = 20 + (rand(-3, 3) / 1);
                    break;
                default:
                    $values[] = rand(0, 100);
            }
        }
        
        return $values;
    }
}

// Calibration Filter
class CalibrationFilter implements DataFilter
{
    private array $calibrationData;
    
    public function __construct(array $calibrationData = [])
    {
        $this->calibrationData = $calibrationData;
    }
    
    public function filter(array $data, array $params = null): array
    {
        if (!isset($data['value']) || !is_numeric($data['value'])) {
            return $data;
        }
        
        $deviceId = $data['device_id'] ?? 'unknown';
        $value = $data['value'];
        
        if (isset($this->calibrationData[$deviceId])) {
            $calibration = $this->calibrationData[$deviceId];
            $calibratedValue = $this->applyCalibration($value, $calibration);
            
            $data['original_value'] = $value;
            $data['calibrated_value'] = $calibratedValue;
            $data['calibration_applied'] = true;
        } else {
            $data['calibrated_value'] = $value;
            $data['calibration_applied'] = false;
        }
        
        return $data;
    }
    
    private function applyCalibration(float $value, array $calibration): float
    {
        // Linear calibration: y = mx + b
        $slope = $calibration['slope'] ?? 1.0;
        $offset = $calibration['offset'] ?? 0.0;
        
        return ($value * $slope) + $offset;
    }
    
    public function setCalibrationData(string $deviceId, array $calibration): void
    {
        $this->calibrationData[$deviceId] = $calibration;
    }
}

// Range Filter
class RangeFilter implements DataFilter
{
    private array $ranges;
    
    public function __construct(array $ranges = [])
    {
        $this->ranges = $ranges;
    }
    
    public function filter(array $data, array $params = null): array
    {
        if (!isset($data['value']) || !is_numeric($data['value'])) {
            return $data;
        }
        
        $deviceType = $data['device_type'] ?? 'unknown';
        $value = $data['value'];
        
        if (isset($this->ranges[$deviceType])) {
            $range = $this->ranges[$deviceType];
            $min = $range['min'] ?? 0;
            $max = $range['max'] ?? 100;
            
            if ($value < $min) {
                $data['value'] = $min;
                $data['range_violation'] = 'below_min';
            } elseif ($value > $max) {
                $data['value'] = $max;
                $data['range_violation'] = 'above_max';
            } else {
                $data['range_violation'] = 'none';
            }
        }
        
        return $data;
    }
    
    public function setRange(string $deviceType, float $min, float $max): void
    {
        $this->ranges[$deviceType] = ['min' => $min, 'max' => $max];
    }
}

// Data Aggregator
class DataAggregator
{
    public function process(array $data, array $params = null): array
    {
        $aggregationType = $params['type'] ?? 'average';
        $timeWindow = $params['window'] ?? 60; // seconds
        $groupBy = $params['group_by'] ?? 'device_id';
        
        if (!isset($data[$groupBy])) {
            return $data;
        }
        
        // Simulate aggregation
        $aggregatedData = [
            'aggregated' => true,
            'aggregation_type' => $aggregationType,
            'time_window' => $timeWindow,
            'group_by' => $groupBy,
            'group_value' => $data[$groupBy],
            'aggregated_value' => $this->calculateAggregatedValue($data, $aggregationType)
        ];
        
        return array_merge($data, $aggregatedData);
    }
    
    private function calculateAggregatedValue(array $data, string $type): float
    {
        $value = $data['value'] ?? 0;
        
        switch ($type) {
            case 'average':
                return $value * 0.9; // Simulate average
            case 'sum':
                return $value * 5; // Simulate sum
            case 'min':
                return $value * 0.8; // Simulate min
            case 'max':
                return $value * 1.2; // Simulate max
            default:
                return $value;
        }
    }
}

// Data Normalizer
class DataNormalizer
{
    public function process(array $data, array $params = null): array
    {
        if (!isset($data['value']) || !is_numeric($data['value'])) {
            return $data;
        }
        
        $method = $params['method'] ?? 'min_max';
        $value = $data['value'];
        
        switch ($method) {
            case 'min_max':
                $normalizedValue = $this->minMaxNormalize($value);
                break;
            case 'z_score':
                $normalizedValue = $this->zScoreNormalize($value);
                break;
            case 'decimal':
                $normalizedValue = $this->decimalNormalize($value);
                break;
            default:
                $normalizedValue = $value;
        }
        
        $data['normalized_value'] = $normalizedValue;
        $data['normalization_method'] = $method;
        
        return $data;
    }
    
    private function minMaxNormalize(float $value): float
    {
        // Simulate min-max normalization (0 to 1)
        $min = 0;
        $max = 100;
        
        return ($value - $min) / ($max - $min);
    }
    
    private function zScoreNormalize(float $value): float
    {
        // Simulate z-score normalization
        $mean = 50;
        $stdDev = 20;
        
        return ($value - $mean) / $stdDev;
    }
    
    private function decimalNormalize(float $value): float
    {
        // Normalize to 0-100 range
        return min(100, max(0, $value));
    }
}

// Data Compressor
class DataCompressor
{
    public function process(array $data, array $params = null): array
    {
        $method = $params['method'] ?? 'lossless';
        
        switch ($method) {
            case 'lossless':
                $compressedData = $this->losslessCompress($data);
                break;
            case 'lossy':
                $compressedData = $this->lossyCompress($data);
                break;
            default:
                $compressedData = $data;
        }
        
        $data['compressed'] = true;
        $data['compression_method'] = $method;
        $data['compressed_size'] = strlen(json_encode($compressedData));
        $data['original_size'] = strlen(json_encode($data));
        
        return $data;
    }
    
    private function losslessCompress(array $data): array
    {
        // Simulate lossless compression
        return array_intersect_key($data, array_flip(['device_id', 'timestamp', 'value']));
    }
    
    private function lossyCompress(array $data): array
    {
        // Simulate lossy compression
        return [
            'device_id' => $data['device_id'] ?? '',
            'timestamp' => $data['timestamp'] ?? 0,
            'value' => round($data['value'] ?? 0, 2)
        ];
    }
}

// Data Validator
class DataValidator
{
    private array $rules;
    
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }
    
    public function process(array $data, array $params = null): array
    {
        $isValid = true;
        $errors = [];
        
        // Check required fields
        $requiredFields = ['device_id', 'timestamp', 'value'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $isValid = false;
                $errors[] = "Missing required field: $field";
            }
        }
        
        // Check data types
        if (isset($data['timestamp']) && !is_numeric($data['timestamp'])) {
            $isValid = false;
            $errors[] = "Invalid timestamp format";
        }
        
        if (isset($data['value']) && !is_numeric($data['value'])) {
            $isValid = false;
            $errors[] = "Invalid value format";
        }
        
        // Check custom rules
        if (isset($this->rules[$data['device_type']])) {
            $rules = $this->rules[$data['device_type']];
            
            foreach ($rules as $rule => $ruleValue) {
                switch ($rule) {
                    case 'min_value':
                        if ($data['value'] < $ruleValue) {
                            $isValid = false;
                            $errors[] = "Value below minimum: $ruleValue";
                        }
                        break;
                    case 'max_value':
                        if ($data['value'] > $ruleValue) {
                            $isValid = false;
                            $errors[] = "Value above maximum: $ruleValue";
                        }
                        break;
                }
            }
        }
        
        $data['is_valid'] = $isValid;
        $data['validation_errors'] = $errors;
        
        return $data;
    }
    
    public function addRule(string $deviceType, string $rule, $value): void
    {
        if (!isset($this->rules[$deviceType])) {
            $this->rules[$deviceType] = [];
        }
        
        $this->rules[$deviceType][$rule] = $value;
    }
}

// Data Analyzer
class DataAnalyzer
{
    public function analyze(array $data): array
    {
        $analysis = [
            'analysis_timestamp' => time(),
            'data_points' => count($data),
            'insights' => []
        ];
        
        if (empty($data)) {
            return $analysis;
        }
        
        // Group by device type
        $groupedData = [];
        foreach ($data as $reading) {
            $deviceType = $reading['device_type'] ?? 'unknown';
            $groupedData[$deviceType][] = $reading;
        }
        
        foreach ($groupedData as $deviceType => $readings) {
            $deviceAnalysis = $this->analyzeDeviceType($readings, $deviceType);
            $analysis['insights'][$deviceType] = $deviceAnalysis;
        }
        
        return $analysis;
    }
    
    private function analyzeDeviceType(array $readings, string $deviceType): array
    {
        $insights = [
            'device_count' => count($readings),
            'time_span' => 0,
            'patterns' => []
        ];
        
        if (count($readings) > 1) {
            $firstTimestamp = $readings[0]['timestamp'] ?? 0;
            $lastTimestamp = end($readings)['timestamp'] ?? 0;
            $insights['time_span'] = $lastTimestamp - $firstTimestamp;
        }
        
        switch ($deviceType) {
            case 'temperature_sensor':
                $insights['patterns'] = $this->analyzeTemperaturePatterns($readings);
                break;
            case 'motion_sensor':
                $insights['patterns'] = $this->analyzeMotionPatterns($readings);
                break;
            case 'smart_light':
                $insights['patterns'] = $this->analyzeLightPatterns($readings);
                break;
        }
        
        return $insights;
    }
    
    private function analyzeTemperaturePatterns(array $readings): array
    {
        $temperatures = array_column($readings, 'temperature');
        
        if (empty($temperatures)) {
            return [];
        }
        
        $patterns = [
            'average' => array_sum($temperatures) / count($temperatures),
            'min' => min($temperatures),
            'max' => max($temperatures),
            'variance' => $this->calculateVariance($temperatures),
            'trend' => $this->calculateTrend($temperatures)
        ];
        
        // Detect anomalies
        $mean = $patterns['average'];
        $stdDev = sqrt($patterns['variance']);
        
        $anomalies = [];
        foreach ($temperatures as $i => $temp) {
            $zScore = ($temp - $mean) / $stdDev;
            if (abs($zScore) > 2) {
                $anomalies[] = ['index' => $i, 'value' => $temp, 'z_score' => $zScore];
            }
        }
        
        $patterns['anomalies'] = $anomalies;
        $patterns['anomaly_count'] = count($anomalies);
        
        return $patterns;
    }
    
    private function analyzeMotionPatterns(array $readings): array
    {
        $detections = array_column($readings, 'motion_detected');
        
        if (empty($detections)) {
            return [];
        }
        
        $trueDetections = array_filter($detections, fn($d) => $d);
        
        $patterns = [
            'total_detections' => count($trueDetections),
            'detection_rate' => count($trueDetections) / count($detections),
            'most_active_period' => $this->findMostActivePeriod($readings),
            'activity_clusters' => $this->findActivityClusters($detections)
        ];
        
        return $patterns;
    }
    
    private function analyzeLightPatterns(array $readings): array
    {
        $brightnesses = array_column($readings, 'brightness');
        $states = array_column($readings, 'is_on');
        
        if (empty($brightnesses)) {
            return [];
        }
        
        $onCount = count(array_filter($states, fn($s) => $s));
        
        $patterns = [
            'average_brightness' => array_sum($brightnesses) / count($brightnesses),
            'on_percentage' => ($onCount / count($states)) * 100,
            'peak_usage_time' => $this->findPeakUsageTime($readings),
            'brightness_variance' => $this->calculateVariance($brightnesses)
        ];
        
        return $patterns;
    }
    
    private function calculateVariance(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $variance = 0;
        
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        
        return $variance / count($values);
    }
    
    private function calculateTrend(array $values): string
    {
        if (count($values) < 2) {
            return 'stable';
        }
        
        $first = $values[0];
        $last = end($values);
        
        $change = ($last - $first) / $first;
        
        if ($change > 0.05) {
            return 'increasing';
        } elseif ($change < -0.05) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }
    
    private function findMostActivePeriod(array $readings): string
    {
        // Simplified - return a time range
        return '18:00-22:00';
    }
    
    private function findActivityClusters(array $detections): array
    {
        // Simplified - return cluster information
        return [
            'cluster_count' => 3,
            'average_cluster_size' => 5
        ];
    }
    
    private function findPeakUsageTime(array $readings): string
    {
        // Simplified - return peak time
        return '20:00';
    }
}

// Sensor Data Processing Examples
class SensorDataProcessingExamples
{
    public function demonstrateDataProcessing(): void
    {
        echo "Sensor Data Processing Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $processor = new SensorDataProcessor();
        
        // Sample sensor data
        $sensorData = [
            [
                'device_id' => 'temp_001',
                'device_type' => 'temperature_sensor',
                'timestamp' => time(),
                'value' => 25.5,
                'status' => 'normal'
            ],
            [
                'device_id' => 'motion_001',
                'device_type' => 'motion_sensor',
                'timestamp' => time(),
                'value' => 1,
                'status' => 'normal'
            ],
            [
                'device_id' => 'light_001',
                'device_type' => 'smart_light',
                'timestamp' => time(),
                'value' => 75,
                'status' => 'normal'
            ]
        ];
        
        echo "Original data:\n";
        foreach ($sensorData as $data) {
            echo "  {$data['device_id']}: {$data['value']}\n";
        }
        
        // Apply filters
        echo "\nApplying filters:\n";
        
        $processingSteps = ['outlier', 'noise', 'smoothing:moving_average'];
        
        foreach ($sensorData as $data) {
            $processed = $processor->processData($data, $processingSteps);
            
            echo "  {$data['device_id']}:\n";
            echo "    Original: {$data['value']}\n";
            echo "    Processed: " . ($processed['filtered_value'] ?? $processed['smoothed_value'] ?? 'N/A') . "\n";
            
            if (isset($processed['outlier'])) {
                echo "    Outlier: " . ($processed['outlier'] ? 'Yes' : 'No') . "\n";
            }
        }
        
        // Add data to buffer
        echo "\nAdding data to buffer:\n";
        foreach ($sensorData as $data) {
            $processor->addData($data);
        }
        
        echo "Buffer size: " . count($processor->getBuffer()) . "\n";
        
        // Get statistics
        echo "\nStatistics:\n";
        $stats = $processor->getStatistics();
        foreach ($stats as $key => $value) {
            if (is_array($value)) {
                echo "  $key: " . json_encode($value) . "\n";
            } else {
                echo "  $key: $value\n";
            }
        }
    }
    
    public function demonstrateAdvancedFiltering(): void
    {
        echo "\nAdvanced Filtering Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $processor = new SensorDataProcessor();
        
        // Set up calibration data
        $calibrationFilter = $processor->getFilters()['calibration'];
        $calibrationFilter->setCalibrationData('temp_001', ['slope' => 1.1, 'offset' => -2.0]);
        $calibrationFilter->setCalibrationData('temp_002', ['slope' => 0.95, 'offset' => 1.5]);
        
        // Set up range filters
        $rangeFilter = $processor->getFilters()['range'];
        $rangeFilter->setRange('temperature_sensor', -40, 125);
        $rangeFilter->setRange('motion_sensor', 0, 1);
        $rangeFilter->setRange('smart_light', 0, 100);
        
        // Test data with various issues
        $testData = [
            [
                'device_id' => 'temp_001',
                'device_type' => 'temperature_sensor',
                'timestamp' => time(),
                'value' => 35.0, // High value
                'status' => 'normal'
            ],
            [
                'device_id' => 'temp_002',
                'device_type' => 'temperature_sensor',
                'timestamp' => time(),
                'value' => -50.0, // Below range
                'status' => 'normal'
            ],
            [
                'device_id' => 'motion_001',
                'device_type' => 'motion_sensor',
                'timestamp' => time(),
                'value' => 5, // Above range
                'status' => 'normal'
            ]
        ];
        
        echo "Test data:\n";
        foreach ($testData as $data) {
            echo "  {$data['device_id']}: {$data['value']}\n";
        }
        
        // Apply calibration and range filtering
        echo "\nApplying calibration and range filtering:\n";
        
        foreach ($testData as $data) {
            echo "\n  {$data['device_id']}:\n";
            
            // Calibration
            $calibrated = $processor->processData($data, ['calibration']);
            echo "    Calibrated: " . ($calibrated['calibrated_value'] ?? 'N/A') . "\n";
            
            // Range filtering
            $rangeFiltered = $processor->processData($calibrated, ['range']);
            echo "    Range filtered: " . ($rangeFiltered['value'] ?? 'N/A') . "\n";
            
            if (isset($rangeFiltered['range_violation'])) {
                echo "    Range violation: {$rangeFiltered['range_violation']}\n";
            }
        }
        
        // Test outlier detection
        echo "\nTesting outlier detection:\n";
        
        $outlierData = [
            'device_id' => 'temp_001',
            'device_type' => 'temperature_sensor',
            'timestamp' => time(),
            'value' => 100.0, // Extreme value
            'status' => 'normal'
        ];
        
        $outlierResult = $processor->processData($outlierData, ['outlier']);
        echo "  Value: {$outlierData['value']}\n";
        echo "  Is outlier: " . ($outlierResult['outlier'] ? 'Yes' : 'No') . "\n";
        echo "  Z-score: " . ($outlierResult['z_score'] ?? 'N/A') . "\n";
    }
    
    public function demonstrateDataAnalysis(): void
    {
        echo "\nData Analysis Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $processor = new SensorDataProcessor();
        
        // Generate sample data
        $sampleData = [];
        for ($i = 0; $i < 50; $i++) {
            $sampleData[] = [
                'device_id' => 'temp_001',
                'device_type' => 'temperature_sensor',
                'timestamp' => time() - ($i * 60), // 1 minute intervals
                'value' => 20 + (rand(-10, 10) / 2),
                'status' => 'normal'
            ];
        }
        
        for ($i = 0; $i < 50; $i++) {
            $sampleData[] = [
                'device_id' => 'motion_001',
                'device_type' => 'motion_sensor',
                'timestamp' => time() - ($i * 60),
                'value' => rand(0, 1),
                'status' => 'normal'
            ];
        }
        
        echo "Generated " . count($sampleData) . " data points\n";
        
        // Add to buffer
        foreach ($sampleData as $data) {
            $processor->addData($data);
        }
        
        // Analyze data
        echo "\nAnalyzing data...\n";
        $analyzer = new DataAnalyzer();
        $analysis = $analyzer->analyze($processor->getBuffer());
        
        echo "Analysis results:\n";
        echo "  Total data points: {$analysis['data_points']}\n";
        echo "  Analysis timestamp: " . date('Y-m-d H:i:s', $analysis['analysis_timestamp']) . "\n";
        
        foreach ($analysis['insights'] as $deviceType => $insights) {
            echo "\n  $deviceType:\n";
            echo "    Device count: {$insights['device_count']}\n";
            echo "    Time span: {$insights['time_span']} seconds\n";
            
            foreach ($insights['patterns'] as $pattern => $value) {
                if (is_array($value)) {
                    echo "    $pattern: " . json_encode($value) . "\n";
                } else {
                    echo "    $pattern: $value\n";
                }
            }
        }
        
        // Export data
        echo "\nExporting data:\n";
        
        $jsonExport = $processor->exportData('json');
        echo "JSON export size: " . strlen($jsonExport) . " bytes\n";
        
        $csvExport = $processor->exportData('csv');
        echo "CSV export size: " . strlen($csvExport) . " bytes\n";
        
        $xmlExport = $processor->exportData('xml');
        echo "XML export size: " . strlen($xmlExport) . " bytes\n";
    }
    
    public function demonstrateRealTimeProcessing(): void
    {
        echo "\nReal-time Processing Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $processor = new SensorDataProcessor();
        
        // Set up validation rules
        $validator = $processor->getProcessors()['validator'];
        $validator->addRule('temperature_sensor', 'min_value', -40);
        $validator->addRule('temperature_sensor', 'max_value', 125);
        $validator->addRule('motion_sensor', 'min_value', 0);
        $validator->addRule('motion_sensor', 'max_value', 1);
        
        echo "Processing real-time sensor data...\n";
        
        // Simulate real-time data stream
        for ($i = 0; $i < 10; $i++) {
            echo "\nReading $i:\n";
            
            // Generate sensor data
            $sensorData = [
                'device_id' => 'temp_001',
                'device_type' => 'temperature_sensor',
                'timestamp' => time(),
                'value' => 20 + (rand(-5, 5) / 2),
                'status' => 'normal'
            ];
            
            // Process through pipeline
            $pipeline = ['outlier', 'noise', 'calibration', 'range', 'validator'];
            $processed = $processor->processData($sensorData, $pipeline);
            
            echo "  Original: {$sensorData['value']}\n";
            echo "  Processed: " . ($processed['calibrated_value'] ?? $processed['value'] ?? 'N/A') . "\n";
            echo "  Valid: " . ($processed['is_valid'] ? 'Yes' : 'No') . "\n";
            
            if (!$processed['is_valid']) {
                echo "  Errors: " . implode(', ', $processed['validation_errors']) . "\n";
            }
            
            // Add to buffer
            $processor->addData($processed);
            
            // Show buffer statistics
            if ($i % 3 === 0) {
                $stats = $processor->getStatistics();
                echo "  Buffer size: {$stats['buffer_size']}\n";
            }
            
            usleep(100000); // 0.1 second delay
        }
        
        // Final analysis
        echo "\nFinal analysis:\n";
        $analyzer = new DataAnalyzer();
        $analysis = $analyzer->analyze($processor->getBuffer());
        
        foreach ($analysis['insights'] as $deviceType => $insights) {
            echo "  $deviceType: {$insights['device_count']} readings\n";
            if (isset($insights['patterns']['average'])) {
                echo "    Average: " . round($insights['patterns']['average'], 2) . "\n";
            }
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nSensor Data Processing Best Practices\n";
        echo str_repeat("-", 45) . "\n";
        
        echo "1. Data Collection:\n";
        echo "   • Use consistent data formats\n";
        echo "   • Validate data at collection time\n";
        echo "   • Include metadata with readings\n";
        echo "   • Handle missing data gracefully\n";
        echo "   • Use appropriate sampling rates\n\n";
        
        echo "2. Data Filtering:\n";
        echo "   • Remove outliers appropriately\n";
        echo "   • Apply noise reduction filters\n";
        echo "   • Use calibration corrections\n";
        echo "   • Validate data ranges\n";
        echo "   • Implement adaptive filtering\n\n";
        
        echo "3. Data Processing:\n";
        echo "   • Use efficient algorithms\n";
        echo "   • Process data in real-time\n";
        echo "   • Implement buffering strategies\n";
        echo "   • Use parallel processing\n";
        echo "   • Monitor processing performance\n\n";
        
        echo "4. Data Analysis:\n";
        echo "   • Identify patterns and trends\n";
        echo "   • Detect anomalies automatically\n";
        echo "   • Use statistical methods\n";
        echo "   • Generate actionable insights\n";
        echo "   • Visualize results effectively\n\n";
        
        echo "5. Data Storage:\n";
        echo "   • Use appropriate compression\n";
        echo "   • Implement data retention policies\n";
        echo "   • Use efficient data structures\n";
        echo "   • Backup important data\n";
        echo "   • Ensure data integrity";
    }
    
    public function runAllExamples(): void
    {
        echo "Sensor Data Processing Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateDataProcessing();
        $this->demonstrateAdvancedFiltering();
        $this->demonstrateDataAnalysis();
        $this->demonstrateRealTimeProcessing();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runSensorDataProcessingDemo(): void
{
    $examples = new SensorDataProcessingExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runSensorDataProcessingDemo();
}
?>
