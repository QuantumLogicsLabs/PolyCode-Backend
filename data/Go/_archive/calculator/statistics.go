package main

import (
	"fmt"
	"math"
	"sort"
)

// DescriptiveStatistics holds basic descriptive statistics
type DescriptiveStatistics struct {
	Count    int     `json:"count"`
	Mean     float64 `json:"mean"`
	Median   float64 `json:"median"`
	Mode     []int   `json:"mode"`
	Range    float64 `json:"range"`
	Variance float64 `json:"variance"`
	StdDev   float64 `json:"std_dev"`
	Min      float64 `json:"min"`
	Max      float64 `json:"max"`
	Sum      float64 `json:"sum"`
	Skewness float64 `json:"skewness"`
	Kurtosis float64 `json:"kurtosis"`
}

// Statistics provides statistical analysis functions
type Statistics struct{}

func NewStatistics() *Statistics {
	return &Statistics{}
}

// CalculateDescriptiveStats calculates comprehensive descriptive statistics
func (s *Statistics) CalculateDescriptiveStats(data []float64) (*DescriptiveStatistics, error) {
	if len(data) == 0 {
		return nil, fmt.Errorf("cannot calculate statistics of empty dataset")
	}

	stats := &DescriptiveStatistics{}
	stats.Count = len(data)

	sorted := make([]float64, len(data))
	copy(sorted, data)
	sort.Float64s(sorted)

	stats.Sum = s.sum(data)
	stats.Mean = stats.Sum / float64(stats.Count)
	stats.Min = sorted[0]
	stats.Max = sorted[stats.Count-1]
	stats.Range = stats.Max - stats.Min

	stats.Median, _ = s.Median(data)
	stats.Mode = s.Mode(data)

	stats.Variance, _ = s.Variance(data)
	stats.StdDev, _ = s.StandardDeviation(data)

	stats.Skewness, _ = s.Skewness(data)
	stats.Kurtosis, _ = s.Kurtosis(data)

	return stats, nil
}

func (s *Statistics) sum(data []float64) float64 {
	total := 0.0
	for _, value := range data {
		total += value
	}
	return total
}

func (s *Statistics) Sum(data []float64) (float64, error) {
	if len(data) == 0 {
		return 0, fmt.Errorf("cannot calculate sum of empty dataset")
	}
	return s.sum(data), nil
}

func (s *Statistics) Mean(data []float64) (float64, error) {
	if len(data) == 0 {
		return 0, fmt.Errorf("cannot calculate mean of empty dataset")
	}
	return s.sum(data) / float64(len(data)), nil
}

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

func (s *Statistics) Mode(data []float64) []int {
	if len(data) == 0 {
		return []int{}
	}

	freq := make(map[float64]int)
	for _, value := range data {
		freq[value]++
	}

	maxFreq := 0
	for _, count := range freq {
		if count > maxFreq {
			maxFreq = count
		}
	}

	var modes []int
	for value, count := range freq {
		if count == maxFreq {
			modes = append(modes, int(value))
		}
	}
	sort.Ints(modes)
	return modes
}

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

func (s *Statistics) StandardDeviation(data []float64) (float64, error) {
	variance, err := s.Variance(data)
	if err != nil {
		return 0, err
	}
	return math.Sqrt(variance), nil
}

func (s *Statistics) Skewness(data []float64) (float64, error) {
	if len(data) < 3 {
		return 0, fmt.Errorf("need at least 3 data points for skewness calculation")
	}

	mean, _ := s.Mean(data)
	stdDev, _ := s.StandardDeviation(data)

	if stdDev == 0 {
		return 0, nil
	}

	sum := 0.0
	for _, value := range data {
		normalized := (value - mean) / stdDev
		sum += normalized * normalized * normalized
	}

	n := float64(len(data))
	adjustment := math.Sqrt((n-1)*n) / (n - 2)
	return (sum / n) * adjustment, nil
}

func (s *Statistics) Kurtosis(data []float64) (float64, error) {
	if len(data) < 4 {
		return 0, fmt.Errorf("need at least 4 data points for kurtosis calculation")
	}

	mean, _ := s.Mean(data)
	stdDev, _ := s.StandardDeviation(data)

	if stdDev == 0 {
		return 0, nil
	}

	sum := 0.0
	for _, value := range data {
		normalized := (value - mean) / stdDev
		sum += normalized * normalized * normalized * normalized
	}

	n := float64(len(data))
	adjustment := ((n - 1) * (n + 1)) / ((n - 2) * (n - 3))
	excessKurtosis := (sum/n)*adjustment - 3*((n-1)*(n-1))/((n-2)*(n-3))

	return excessKurtosis, nil
}

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
	corr, _ := s.Correlation(x, y)

	return &LinearRegression{
		Slope:     slope,
		Intercept: intercept,
		RSquared:  corr * corr,
	}, nil
}

func (lr *LinearRegression) Predict(x float64) float64 {
	return lr.Slope*x + lr.Intercept
}

func (s *Statistics) ConfidenceInterval(data []float64, confidence float64) (float64, float64, error) {
	if len(data) < 2 {
		return 0, 0, fmt.Errorf("need at least 2 data points for confidence interval")
	}
	if confidence <= 0 || confidence >= 1 {
		return 0, 0, fmt.Errorf("confidence must be between 0 and 1: %f", confidence)
	}

	mean, _ := s.Mean(data)
	stdDev, _ := s.StandardDeviation(data)

	standardError := stdDev / math.Sqrt(float64(len(data)))

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

	margin := zScore * standardError
	return mean - margin, mean + margin, nil
}

func main() {
	fmt.Println("=== Statistics Calculator Demo ===")
	stats := NewStatistics()

	data := []float64{10, 12, 23, 23, 16, 23, 21, 16}
	desc, _ := stats.CalculateDescriptiveStats(data)

	fmt.Printf("Count: %d | Mean: %.2f | Median: %.2f | Mode: %v\n", desc.Count, desc.Mean, desc.Median, desc.Mode)
	fmt.Printf("Min: %.2f | Max: %.2f | Range: %.2f | StdDev: %.2f\n", desc.Min, desc.Max, desc.Range, desc.StdDev)

	q1, q2, q3, _ := stats.Quartiles(data)
	fmt.Printf("Quartiles: Q1=%.2f, Q2=%.2f, Q3=%.2f\n", q1, q2, q3)

	x := []float64{1, 2, 3, 4, 5}
	y := []float64{2, 4, 6, 8, 10}
	corr, _ := stats.Correlation(x, y)
	reg, _ := stats.LinearRegression(x, y)
	fmt.Printf("Correlation (x, y): %.4f\n", corr)
	fmt.Printf("Linear Fit: y = %.2fx + %.2f (R^2 = %.4f)\n", reg.Slope, reg.Intercept, reg.RSquared)
	fmt.Printf("Prediction for x=6: %.2f\n", reg.Predict(6))

	lower, upper, _ := stats.ConfidenceInterval(data, 0.95)
	fmt.Printf("95%% Confidence Interval: [%.2f, %.2f]\n", lower, upper)
}
