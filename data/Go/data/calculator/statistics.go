package calculator

import (
	"fmt"
	"math"
	"sort"
)

// Statistics provides statistical analysis functions
type Statistics struct {
	scientific *Scientific
}

// NewStatistics creates a new statistics calculator
func NewStatistics() *Statistics {
	return &Statistics{
		scientific: NewScientific(),
	}
}

// DescriptiveStatistics holds basic descriptive statistics
type DescriptiveStatistics struct {
	Count       int     `json:"count"`
	Mean        float64 `json:"mean"`
	Median      float64 `json:"median"`
	Mode        []int   `json:"mode"`
	Range       float64 `json:"range"`
	Variance    float64 `json:"variance"`
	StdDev      float64 `json:"std_dev"`
	Min         float64 `json:"min"`
	Max         float64 `json:"max"`
	Sum         float64 `json:"sum"`
	Skewness    float64 `json:"skewness"`
	Kurtosis    float64 `json:"kurtosis"`
}

// CalculateDescriptiveStats calculates comprehensive descriptive statistics
func (s *Statistics) CalculateDescriptiveStats(data []float64) (*DescriptiveStatistics, error) {
	if len(data) == 0 {
		return nil, fmt.Errorf("cannot calculate statistics of empty dataset")
	}
	
	stats := &DescriptiveStatistics{}
	stats.Count = len(data)
	
	// Sort data for median and range calculations
	sorted := make([]float64, len(data))
	copy(sorted, data)
	sort.Float64s(sorted)
	
	// Basic statistics
	stats.Sum = s.sum(data)
	stats.Mean = stats.Sum / float64(stats.Count)
	stats.Min = sorted[0]
	stats.Max = sorted[stats.Count-1]
	stats.Range = stats.Max - stats.Min
	
	// Median
	stats.Median, _ = s.Median(data)
	
	// Mode
	stats.Mode = s.Mode(data)
	
	// Variance and standard deviation
	stats.Variance, _ = s.Variance(data)
	stats.StdDev, _ = s.StandardDeviation(data)
	
	// Skewness and kurtosis
	stats.Skewness, _ = s.Skewness(data)
	stats.Kurtosis, _ = s.Kurtosis(data)
	
	return stats, nil
}

// Sum calculates the sum of all values
func (s *Statistics) Sum(data []float64) (float64, error) {
	if len(data) == 0 {
		return 0, fmt.Errorf("cannot calculate sum of empty dataset")
	}
	return s.sum(data), nil
}

func (s *Statistics) sum(data []float64) float64 {
	sum := 0.0
	for _, value := range data {
		sum += value
	}
	return sum
}

// Mean calculates the arithmetic mean
func (s *Statistics) Mean(data []float64) (float64, error) {
	if len(data) == 0 {
		return 0, fmt.Errorf("cannot calculate mean of empty dataset")
	}
	return s.sum(data) / float64(len(data)), nil
}

// Median calculates the median value
func (s *Statistics) Median(data []float64) (float64, error) {
	if len(data) == 0 {
		return 0, fmt.Errorf("cannot calculate median of empty dataset")
	}
	
	sorted := make([]float64, len(data))
	copy(sorted, data)
	sort.Float64s(sorted)
	
	mid := len(sorted) / 2
	if len(sorted)%2 == 1 {
		return sorted[mid], nil
	}
	
	return (sorted[mid-1] + sorted[mid]) / 2, nil
}

// Mode calculates the mode(s) of the data
func (s *Statistics) Mode(data []float64) []int {
	if len(data) == 0 {
		return []int{}
	}
	
	// Create frequency map
	freq := make(map[float64]int)
	for _, value := range data {
		freq[value]++
	}
	
	// Find maximum frequency
	maxFreq := 0
	for _, count := range freq {
		if count > maxFreq {
			maxFreq = count
		}
	}
	
	// Collect all values with maximum frequency
	var modes []int
	for value, count := range freq {
		if count == maxFreq {
			modes = append(modes, int(value))
		}
	}
	
	return modes
}

// Variance calculates the sample variance
func (s *Statistics) Variance(data []float64) (float64, error) {
	if len(data) < 2 {
		return 0, fmt.Errorf("need at least 2 data points for variance calculation")
	}
	
	mean, _ := s.Mean(data)
	sumSquaredDiff := 0.0
	
	for _, value := range data {
		diff := value - mean
		sumSquaredDiff += diff * diff
	}
	
	return sumSquaredDiff / float64(len(data)-1), nil
}

// StandardDeviation calculates the sample standard deviation
func (s *Statistics) StandardDeviation(data []float64) (float64, error) {
	variance, err := s.Variance(data)
	if err != nil {
		return 0, err
	}
	
	return math.Sqrt(variance), nil
}

// Skewness measures the asymmetry of the probability distribution
func (s *Statistics) Skewness(data []float64) (float64, error) {
	if len(data) < 3 {
		return 0, fmt.Errorf("need at least 3 data points for skewness calculation")
	}
	
	mean, _ := s.Mean(data)
	stdDev, _ := s.StandardDeviation(data)
	
	if stdDev == 0 {
		return 0, nil // No skewness if no variation
	}
	
	sum := 0.0
	for _, value := range data {
		normalized := (value - mean) / stdDev
		sum += normalized * normalized * normalized
	}
	
	// Adjust for sample size
	n := float64(len(data))
	adjustment := math.Sqrt((n - 1) * n) / (n - 2)
	
	return (sum / n) * adjustment, nil
}

// Kurtosis measures the "tailedness" of the probability distribution
func (s *Statistics) Kurtosis(data []float64) (float64, error) {
	if len(data) < 4 {
		return 0, fmt.Errorf("need at least 4 data points for kurtosis calculation")
	}
	
	mean, _ := s.Mean(data)
	stdDev, _ := s.StandardDeviation(data)
	
	if stdDev == 0 {
		return 0, nil // No kurtosis if no variation
	}
	
	sum := 0.0
	for _, value := range data {
		normalized := (value - mean) / stdDev
		sum += normalized * normalized * normalized * normalized
	}
	
	// Excess kurtosis (subtract 3 for normal distribution)
	n := float64(len(data))
	adjustment := ((n - 1) * (n + 1)) / ((n - 2) * (n - 3))
	excessKurtosis := (sum / n) * adjustment - 3 * ((n-1)*(n-1))/((n-2)*(n-3))
	
	return excessKurtosis, nil
}

// Percentile calculates the p-th percentile
func (s *Statistics) Percentile(data []float64, p float64) (float64, error) {
	if len(data) == 0 {
		return 0, fmt.Errorf("cannot calculate percentile of empty dataset")
	}
	if p < 0 || p > 100 {
		return 0, fmt.Errorf("percentile must be between 0 and 100: %f", p)
	}
	
	sorted := make([]float64, len(data))
	copy(sorted, data)
	sort.Float64s(sorted)
	
	index := (p / 100) * float64(len(sorted)-1)
	lower := int(math.Floor(index))
	upper := int(math.Ceil(index))
	
	if lower == upper {
		return sorted[lower], nil
	}
	
	weight := index - float64(lower)
	return sorted[lower]*(1-weight) + sorted[upper]*weight, nil
}

// Quartiles calculates the quartiles (Q1, Q2, Q3)
func (s *Statistics) Quartiles(data []float64) (q1, q2, q3 float64, err error) {
	q1, err = s.Percentile(data, 25)
	if err != nil {
		return 0, 0, 0, err
	}
	
	q2, err = s.Percentile(data, 50)
	if err != nil {
		return 0, 0, 0, err
	}
	
	q3, err = s.Percentile(data, 75)
	if err != nil {
		return 0, 0, 0, err
	}
	
	return q1, q2, q3, nil
}

// InterquartileRange calculates the IQR (Q3 - Q1)
func (s *Statistics) InterquartileRange(data []float64) (float64, error) {
	q1, _, q3, err := s.Quartiles(data)
	if err != nil {
		return 0, err
	}
	
	return q3 - q1, nil
}

// Outliers identifies outliers using the IQR method
func (s *Statistics) Outliers(data []float64) ([]float64, []float64, error) {
	if len(data) == 0 {
		return nil, nil, fmt.Errorf("cannot find outliers in empty dataset")
	}
	
	q1, _, q3, err := s.Quartiles(data)
	if err != nil {
		return nil, nil, err
	}
	
	iqr := q3 - q1
	lowerBound := q1 - 1.5*iqr
	upperBound := q3 + 1.5*iqr
	
	var lowerOutliers, upperOutliers []float64
	
	for _, value := range data {
		if value < lowerBound {
			lowerOutliers = append(lowerOutliers, value)
		} else if value > upperBound {
			upperOutliers = append(upperOutliers, value)
		}
	}
	
	return lowerOutliers, upperOutliers, nil
}

// Correlation calculates the Pearson correlation coefficient
func (s *Statistics) Correlation(x, y []float64) (float64, error) {
	if len(x) != len(y) {
		return 0, fmt.Errorf("datasets must have the same length: x=%d, y=%d", len(x), len(y))
	}
	if len(x) < 2 {
		return 0, fmt.Errorf("need at least 2 data points for correlation calculation")
	}
	
	meanX, _ := s.Mean(x)
	meanY, _ := s.Mean(y)
	
	var numerator, sumXSquared, sumYSquared float64
	
	for i := 0; i < len(x); i++ {
		diffX := x[i] - meanX
		diffY := y[i] - meanY
		
		numerator += diffX * diffY
		sumXSquared += diffX * diffX
		sumYSquared += diffY * diffY
	}
	
	denominator := math.Sqrt(sumXSquared * sumYSquared)
	if denominator == 0 {
		return 0, fmt.Errorf("cannot calculate correlation: zero variance")
	}
	
	return numerator / denominator, nil
}

// Covariance calculates the covariance between two datasets
func (s *Statistics) Covariance(x, y []float64) (float64, error) {
	if len(x) != len(y) {
		return 0, fmt.Errorf("datasets must have the same length: x=%d, y=%d", len(x), len(y))
	}
	if len(x) < 2 {
		return 0, fmt.Errorf("need at least 2 data points for covariance calculation")
	}
	
	meanX, _ := s.Mean(x)
	meanY, _ := s.Mean(y)
	
	sum := 0.0
	for i := 0; i < len(x); i++ {
		sum += (x[i] - meanX) * (y[i] - meanY)
	}
	
	return sum / float64(len(x)-1), nil
}

// LinearRegression performs simple linear regression
type LinearRegression struct {
	Slope     float64 `json:"slope"`
	Intercept float64 `json:"intercept"`
	RSquared  float64 `json:"r_squared"`
}

func (s *Statistics) LinearRegression(x, y []float64) (*LinearRegression, error) {
	if len(x) != len(y) {
		return nil, fmt.Errorf("datasets must have the same length: x=%d, y=%d", len(x), len(y))
	}
	if len(x) < 2 {
		return nil, fmt.Errorf("need at least 2 data points for linear regression")
	}
	
	meanX, _ := s.Mean(x)
	meanY, _ := s.Mean(y)
	
	var numerator, denominator float64
	
	for i := 0; i < len(x); i++ {
		numerator += (x[i] - meanX) * (y[i] - meanY)
		denominator += (x[i] - meanX) * (x[i] - meanX)
	}
	
	if denominator == 0 {
		return nil, fmt.Errorf("cannot perform linear regression: zero variance in x")
	}
	
	slope := numerator / denominator
	intercept := meanY - slope*meanX
	
	// Calculate R-squared
	corr, _ := s.Correlation(x, y)
	rSquared := corr * corr
	
	return &LinearRegression{
		Slope:     slope,
		Intercept: intercept,
		RSquared:  rSquared,
	}, nil
}

// Predict uses the linear regression model to predict y for a given x
func (lr *LinearRegression) Predict(x float64) float64 {
	return lr.Slope*x + lr.Intercept
}

// MovingAverage calculates the moving average with a given window size
func (s *Statistics) MovingAverage(data []float64, windowSize int) ([]float64, error) {
	if len(data) == 0 {
		return nil, fmt.Errorf("cannot calculate moving average of empty dataset")
	}
	if windowSize <= 0 || windowSize > len(data) {
		return nil, fmt.Errorf("window size must be between 1 and %d: %d", len(data), windowSize)
	}
	
	result := make([]float64, len(data)-windowSize+1)
	
	for i := 0; i <= len(data)-windowSize; i++ {
		window := data[i : i+windowSize]
		mean, _ := s.Mean(window)
		result[i] = mean
	}
	
	return result, nil
}

// ExponentialMovingAverage calculates the exponential moving average
func (s *Statistics) ExponentialMovingAverage(data []float64, alpha float64) ([]float64, error) {
	if len(data) == 0 {
		return nil, fmt.Errorf("cannot calculate EMA of empty dataset")
	}
	if alpha <= 0 || alpha > 1 {
		return nil, fmt.Errorf("alpha must be between 0 and 1: %f", alpha)
	}
	
	ema := make([]float64, len(data))
	ema[0] = data[0]
	
	for i := 1; i < len(data); i++ {
		ema[i] = alpha*data[i] + (1-alpha)*ema[i-1]
	}
	
	return ema, nil
}

// ZScore calculates the z-score for each value
func (s *Statistics) ZScore(data []float64) ([]float64, error) {
	if len(data) == 0 {
		return nil, fmt.Errorf("cannot calculate z-scores of empty dataset")
	}
	
	mean, _ := s.Mean(data)
	stdDev, _ := s.StandardDeviation(data)
	
	if stdDev == 0 {
		return nil, fmt.Errorf("cannot calculate z-scores: zero standard deviation")
	}
	
	zScores := make([]float64, len(data))
	for i, value := range data {
		zScores[i] = (value - mean) / stdDev
	}
	
	return zScores, nil
}

// Histogram creates histogram bins
type HistogramBin struct {
	RangeStart float64 `json:"range_start"`
	RangeEnd   float64 `json:"range_end"`
	Count      int     `json:"count"`
	Frequency  float64 `json:"frequency"`
}

func (s *Statistics) Histogram(data []float64, numBins int) ([]HistogramBin, error) {
	if len(data) == 0 {
		return nil, fmt.Errorf("cannot create histogram of empty dataset")
	}
	if numBins <= 0 {
		return nil, fmt.Errorf("number of bins must be positive: %d", numBins)
	}
	
	min, max := data[0], data[0]
	for _, value := range data {
		if value < min {
			min = value
		}
		if value > max {
			max = value
		}
	}
	
	binWidth := (max - min) / float64(numBins)
	bins := make([]HistogramBin, numBins)
	
	for i := 0; i < numBins; i++ {
		bins[i].RangeStart = min + float64(i)*binWidth
		bins[i].RangeEnd = min + float64(i+1)*binWidth
	}
	
	// Count values in each bin
	for _, value := range data {
		binIndex := int((value - min) / binWidth)
		if binIndex >= numBins {
			binIndex = numBins - 1 // Handle edge case for max value
		}
		bins[binIndex].Count++
	}
	
	// Calculate frequencies
	for i := range bins {
		bins[i].Frequency = float64(bins[i].Count) / float64(len(data))
	}
	
	return bins, nil
}

// ConfidenceInterval calculates confidence interval for the mean
func (s *Statistics) ConfidenceInterval(data []float64, confidence float64) (float64, float64, error) {
	if len(data) < 2 {
		return 0, 0, fmt.Errorf("need at least 2 data points for confidence interval")
	}
	if confidence <= 0 || confidence >= 1 {
		return 0, 0, fmt.Errorf("confidence must be between 0 and 1: %f", confidence)
	}
	
	mean, _ := s.Mean(data)
	stdDev, _ := s.StandardDeviation(data)
	
	// For large samples, use z-distribution approximation
	// For small samples, use t-distribution (simplified here)
	n := float64(len(data))
	standardError := stdDev / math.Sqrt(n)
	
	// Simplified z-score for 95% confidence (1.96) and 99% confidence (2.576)
	var zScore float64
	switch {
	case confidence >= 0.95:
		zScore = 1.96
	case confidence >= 0.90:
		zScore = 1.645
	default:
		zScore = 1.96 // Default to 95%
	}
	
	margin := zScore * standardError
	lower := mean - margin
	upper := mean + margin
	
	return lower, upper, nil
}

// SampleSize calculates required sample size for given margin of error
func (s *Statistics) SampleSize(confidence float64, marginOfError float64, populationStdDev float64) (int, error) {
	if confidence <= 0 || confidence >= 1 {
		return 0, fmt.Errorf("confidence must be between 0 and 1: %f", confidence)
	}
	if marginOfError <= 0 {
		return 0, fmt.Errorf("margin of error must be positive: %f", marginOfError)
	}
	
	// Simplified z-score
	var zScore float64
	switch {
	case confidence >= 0.99:
		zScore = 2.576
	case confidence >= 0.95:
		zScore = 1.96
	case confidence >= 0.90:
		zScore = 1.645
	default:
		zScore = 1.96
	}
	
	sampleSize := (zScore * zScore * populationStdDev * populationStdDev) / (marginOfError * marginOfError)
	
	// Round up to nearest integer
	return int(math.Ceil(sampleSize)), nil
}
