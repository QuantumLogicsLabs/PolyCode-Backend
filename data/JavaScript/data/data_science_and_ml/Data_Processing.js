/**
 * Data Processing with JavaScript
 * This example demonstrates simple data manipulation and analysis
 * using modern JavaScript features.
 */

const data = [
    { id: 1, name: 'John Doe', age: 25, score: 85 },
    { id: 2, name: 'Jane Smith', age: 32, score: 92 },
    { id: 3, name: 'Mike Johnson', age: 45, score: 78 },
    { id: 4, name: 'Emily Davis', age: 28, score: 95 },
    { id: 5, name: 'Chris Lee', age: 38, score: 88 }
];

// Basic Analysis Functions
const getAverageScore = (dataset) => {
    const total = dataset.reduce((sum, item) => sum + item.score, 0);
    return total / dataset.length;
};

const getTopPerformers = (dataset, threshold) => {
    return dataset.filter(item => item.score >= threshold);
};

const groupByCategory = (dataset, categoryKey) => {
    return dataset.reduce((groups, item) => {
        const key = item[categoryKey];
        if (!groups[key]) groups[key] = [];
        groups[key].push(item);
        return groups;
    }, {});
};

// Data Transformation
const summaryData = data.map(item => ({
    name: item.name,
    status: item.score >= 90 ? 'Excellent' : 'Good'
}));

console.log('--- Data Science Example ---');
console.log('Average Score:', getAverageScore(data));
console.log('Top Performers (score >= 90):', getTopPerformers(data, 90));
console.log('Summary Data:', summaryData);

// Linear Regression Mock Implementation
const linearRegression = (points) => {
    const n = points.length;
    let sumX = 0, sumY = 0, sumXY = 0, sumX2 = 0;
    
    for (const point of points) {
        sumX += point.x;
        sumY += point.y;
        sumXY += point.x * point.y;
        sumX2 += point.x * point.x;
    }
    
    const slope = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);
    const intercept = (sumY - slope * sumX) / n;
    
    return { slope, intercept, predict: (x) => slope * x + intercept };
};

const lr = linearRegression([
    {x: 1, y: 2}, {x: 2, y: 3}, {x: 3, y: 5}, {x: 4, y: 4}, {x: 5, y: 6}
]);

console.log('Linear Regression Prediction (x=6):', lr.predict(6));