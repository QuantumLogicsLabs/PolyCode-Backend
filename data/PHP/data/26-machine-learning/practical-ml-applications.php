<?php
/**
 * Practical Machine Learning Applications
 * 
 * Real-world ML applications and use cases in PHP.
 */

// Spam Email Classifier
class SpamEmailClassifier
{
    private array $vocabulary = [];
    private array $spamWords = [];
    private array $hamWords = [];
    private int $totalSpam = 0;
    private int $totalHam = 0;
    private float $spamThreshold = 0.5;
    
    public function train(array $emails, array $labels): void
    {
        echo "Training spam classifier...\n";
        
        for ($i = 0; $i < count($emails); $i++) {
            $email = $emails[$i];
            $label = $labels[$i];
            
            $words = $this->extractWords($email);
            
            if ($label === 'spam') {
                $this->totalSpam++;
                foreach ($words as $word) {
                    $this->spamWords[$word] = ($this->spamWords[$word] ?? 0) + 1;
                }
            } else {
                $this->totalHam++;
                foreach ($words as $word) {
                    $this->hamWords[$word] = ($this->hamWords[$word] ?? 0) + 1;
                }
            }
            
            foreach ($words as $word) {
                $this->vocabulary[$word] = true;
            }
        }
        
        echo "Trained on " . count($emails) . " emails\n";
        echo "Vocabulary size: " . count($this->vocabulary) . "\n";
        echo "Spam emails: $this->totalSpam\n";
        echo "Ham emails: $this->totalHam\n\n";
    }
    
    public function predict(string $email): string
    {
        $words = $this->extractWords($email);
        
        $spamScore = 0;
        $hamScore = 0;
        
        foreach ($words as $word) {
            $spamProb = ($this->spamWords[$word] ?? 0) / $this->totalSpam;
            $hamProb = ($this->hamWords[$word] ?? 0) / $this->totalHam;
            
            // Apply Laplace smoothing
            $spamProb = ($spamProb + 1) / ($this->totalSpam + 2);
            $hamProb = ($hamProb + 1) / ($this->totalHam + 2);
            
            $spamScore += log($spamProb);
            $hamScore += log($hamProb);
        }
        
        $totalScore = $spamScore + $hamScore;
        $spamProbability = exp($spamScore) / (exp($spamScore) + exp($hamScore));
        
        return $spamProbability > $this->spamThreshold ? 'spam' : 'ham';
    }
    
    public function predictWithProbability(string $email): array
    {
        $words = $this->extractWords($email);
        
        $spamScore = 0;
        $hamScore = 0;
        
        foreach ($words as $word) {
            $spamProb = ($this->spamWords[$word] ?? 0) / $this->totalSpam;
            $hamProb = ($this->hamWords[$word] ?? 0) / $this->totalHam;
            
            // Apply Laplace smoothing
            $spamProb = ($spamProb + 1) / ($this->totalSpam + 2);
            $hamProb = ($hamProb + 1) / ($this->totalHam + 2);
            
            $spamScore += log($spamProb);
            $hamScore += log($hamProb);
        }
        
        $totalScore = $spamScore + $hamScore;
        $spamProbability = exp($spamScore) / (exp($spamScore) + exp($hamScore));
        
        return [
            'prediction' => $spamProbability > $this->spamThreshold ? 'spam' : 'ham',
            'probability' => $spamProbability
        ];
    }
    
    private function extractWords(string $email): array
    {
        // Convert to lowercase and extract words
        $email = strtolower($email);
        $email = preg_replace('/[^a-z0-9\s]/', ' ', $email);
        $words = explode(' ', $email);
        
        // Remove empty strings and common stop words
        $stopWords = ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'as', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must', 'can', 'this', 'that', 'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they'];
        
        return array_filter($words, function($word) use ($stopWords) {
            return !empty($word) && !in_array($word, $stopWords);
        });
    }
    
    public function setSpamThreshold(float $threshold): void
    {
        $this->spamThreshold = $threshold;
    }
    
    public function getVocabularySize(): int
    {
        return count($this->vocabulary);
    }
    
    public function getTopSpamWords(int $limit = 10): array
    {
        arsort($this->spamWords);
        return array_slice($this->spamWords, 0, $limit, true);
    }
    
    public function getTopHamWords(int $limit = 10): array
    {
        arsort($this->hamWords);
        return array_slice($this->hamWords, 0, $limit, true);
    }
}

// Recommendation System
class RecommendationSystem
{
    private array $userItemMatrix = [];
    private array $itemUserMatrix = [];
    private array $userSimilarities = [];
    private array $itemSimilarities = [];
    private int $numRecommendations = 5;
    
    public function __construct()
    {
        $this->initializeSampleData();
    }
    
    private function initializeSampleData(): void
    {
        // Sample user-item ratings (1-5 scale)
        $this->userItemMatrix = [
            'user1' => [
                'item1' => 5, 'item2' => 3, 'item3' => 4, 'item4' => 4, 'item5' => 2
            ],
            'user2' => [
                'item1' => 3, 'item2' => 1, 'item3' => 2, 'item4' => 3, 'item5' => 3
            ],
            'user3' => [
                'item1' => 4, 'item2' => 2, 'item3' => 5, 'item4' => 3, 'item5' => 1
            ],
            'user4' => [
                'item1' => 3, 'item2' => 5, 'item3' => 3, 'item4' => 4, 'item5' => 4
            ],
            'user5' => [
                'item1' => 1, 'item2' => 4, 'item3' => 3, 'item4' => 2, 'item5' => 5
            ]
        ];
        
        // Build item-user matrix
        foreach ($this->userItemMatrix as $user => $items) {
            foreach ($items as $item => $rating) {
                $this->itemUserMatrix[$item][$user] = $rating;
            }
        }
        
        echo "Initialized recommendation system with " . count($this->userItemMatrix) . " users and " . count($this->itemUserMatrix) . " items\n\n";
    }
    
    public function addUserRatings(string $user, array $ratings): void
    {
        $this->userItemMatrix[$user] = $ratings;
        
        foreach ($ratings as $item => $rating) {
            $this->itemUserMatrix[$item][$user] = $rating;
        }
        
        echo "Added ratings for user: $user\n";
    }
    
    public function calculateUserSimilarities(): void
    {
        echo "Calculating user similarities...\n";
        
        $users = array_keys($this->userItemMatrix);
        
        foreach ($users as $user1) {
            $this->userSimilarities[$user1] = [];
            
            foreach ($users as $user2) {
                if ($user1 !== $user2) {
                    $similarity = $this->calculateCosineSimilarity(
                        $this->userItemMatrix[$user1],
                        $this->userItemMatrix[$user2]
                    );
                    $this->userSimilarities[$user1][$user2] = $similarity;
                }
            }
        }
        
        echo "User similarities calculated\n\n";
    }
    
    public function calculateItemSimilarities(): void
    {
        echo "Calculating item similarities...\n";
        
        $items = array_keys($this->itemUserMatrix);
        
        foreach ($items as $item1) {
            $this->itemSimilarities[$item1] = [];
            
            foreach ($items as $item2) {
                if ($item1 !== $item2) {
                    $similarity = $this->calculateCosineSimilarity(
                        $this->itemUserMatrix[$item1],
                        $this->itemUserMatrix[$item2]
                    );
                    $this->itemSimilarities[$item1][$item2] = $similarity;
                }
            }
        }
        
        echo "Item similarities calculated\n\n";
    }
    
    private function calculateCosineSimilarity(array $vector1, array $vector2): float
    {
        $commonItems = array_intersect_key($vector1, $vector2);
        
        if (empty($commonItems)) {
            return 0;
        }
        
        $dotProduct = 0;
        $norm1 = 0;
        $norm2 = 0;
        
        foreach ($commonItems as $item => $rating) {
            $dotProduct += $rating * $vector2[$item];
            $norm1 += $rating * $rating;
            $norm2 += $vector2[$item] * $vector2[$item];
        }
        
        if ($norm1 == 0 || $norm2 == 0) {
            return 0;
        }
        
        return $dotProduct / (sqrt($norm1) * sqrt($norm2));
    }
    
    public function getUserBasedRecommendations(string $user): array
    {
        if (!isset($this->userItemMatrix[$user])) {
            throw new Exception("User not found: $user");
        }
        
        if (empty($this->userSimilarities)) {
            $this->calculateUserSimilarities();
        }
        
        $userRatings = $this->userItemMatrix[$user];
        $recommendations = [];
        
        // Find similar users
        $similarUsers = $this->userSimilarities[$user] ?? [];
        arsort($similarUsers);
        
        $topSimilarUsers = array_slice($similarUsers, 0, 3, true);
        
        // Get items not rated by the user
        $allItems = array_keys($this->itemUserMatrix);
        $unratedItems = array_diff($allItems, array_keys($userRatings));
        
        foreach ($unratedItems as $item) {
            $score = 0;
            $similaritySum = 0;
            
            foreach ($topSimilarUsers as $similarUser => $similarity) {
                if (isset($this->userItemMatrix[$similarUser][$item])) {
                    $score += $similarity * $this->userItemMatrix[$similarUser][$item];
                    $similaritySum += abs($similarity);
                }
            }
            
            if ($similaritySum > 0) {
                $recommendations[$item] = $score / $similaritySum;
            }
        }
        
        arsort($recommendations);
        return array_slice($recommendations, 0, $this->numRecommendations, true);
    }
    
    public function getItemBasedRecommendations(string $user): array
    {
        if (!isset($this->userItemMatrix[$user])) {
            throw new Exception("User not found: $user");
        }
        
        if (empty($this->itemSimilarities)) {
            $this->calculateItemSimilarities();
        }
        
        $userRatings = $this->userItemMatrix[$user];
        $recommendations = [];
        
        // Get items not rated by the user
        $allItems = array_keys($this->itemUserMatrix);
        $unratedItems = array_diff($allItems, array_keys($userRatings));
        
        foreach ($unratedItems as $item) {
            $score = 0;
            $similaritySum = 0;
            
            // Find similar items to the target item
            $similarItems = $this->itemSimilarities[$item] ?? [];
            arsort($similarItems);
            
            $topSimilarItems = array_slice($similarItems, 0, 3, true);
            
            foreach ($topSimilarItems as $similarItem => $similarity) {
                if (isset($userRatings[$similarItem])) {
                    $score += $similarity * $userRatings[$similarItem];
                    $similaritySum += abs($similarity);
                }
            }
            
            if ($similaritySum > 0) {
                $recommendations[$item] = $score / $similaritySum;
            }
        }
        
        arsort($recommendations);
        return array_slice($recommendations, 0, $this->numRecommendations, true);
    }
    
    public function getHybridRecommendations(string $user): array
    {
        $userBased = $this->getUserBasedRecommendations($user);
        $itemBased = $this->getItemBasedRecommendations($user);
        
        // Combine both approaches (simple weighted average)
        $hybrid = [];
        
        foreach ($userBased as $item => $score) {
            $hybrid[$item] = $score * 0.6; // 60% weight to user-based
        }
        
        foreach ($itemBased as $item => $score) {
            if (isset($hybrid[$item])) {
                $hybrid[$item] += $score * 0.4; // 40% weight to item-based
            } else {
                $hybrid[$item] = $score * 0.4;
            }
        }
        
        arsort($hybrid);
        return array_slice($hybrid, 0, $this->numRecommendations, true);
    }
    
    public function setNumRecommendations(int $num): void
    {
        $this->numRecommendations = $num;
    }
    
    public function getUserRatings(string $user): array
    {
        return $this->userItemMatrix[$user] ?? [];
    }
    
    public function getTopSimilarUsers(string $user, int $limit = 5): array
    {
        if (empty($this->userSimilarities)) {
            $this->calculateUserSimilarities();
        }
        
        $similarities = $this->userSimilarities[$user] ?? [];
        arsort($similarities);
        
        return array_slice($similarities, 0, $limit, true);
    }
}

// Sentiment Analysis
class SentimentAnalyzer
{
    private array $positiveWords = [];
    private array $negativeWords = [];
    private array $neutralWords = [];
    private array $intensifiers = [];
    
    public function __construct()
    {
        $this->initializeWordLists();
    }
    
    private function initializeWordLists(): void
    {
        // Positive words
        $this->positiveWords = [
            'good', 'great', 'excellent', 'amazing', 'wonderful', 'fantastic', 'awesome',
            'brilliant', 'outstanding', 'superb', 'terrific', 'marvelous', 'delightful',
            'pleasing', 'satisfying', 'impressive', 'remarkable', 'exceptional', 'perfect',
            'beautiful', 'nice', 'lovely', 'charming', 'attractive', 'appealing', 'positive',
            'happy', 'joy', 'joyful', 'cheerful', 'glad', 'pleased', 'content', 'satisfied',
            'love', 'like', 'enjoy', 'appreciate', 'recommend', 'favor', 'prefer', 'approve'
        ];
        
        // Negative words
        $this->negativeWords = [
            'bad', 'terrible', 'awful', 'horrible', 'disgusting', 'dreadful', 'appalling',
            'atrocious', 'abysmal', 'deplorable', 'lousy', 'poor', 'inadequate', 'subpar',
            'disappointing', 'unsatisfactory', 'unpleasant', 'uncomfortable', 'painful',
            'annoying', 'frustrating', 'irritating', 'disturbing', 'upsetting', 'worrying',
            'hate', 'dislike', 'despise', 'loathe', 'abhor', 'detest', 'reject', 'oppose',
            'sad', 'angry', 'upset', 'depressed', 'miserable', 'unhappy', 'gloomy', 'dejected'
        ];
        
        // Intensifiers
        $this->intensifiers = [
            'very' => 1.5, 'extremely' => 2.0, 'really' => 1.3, 'quite' => 1.2,
            'too' => 1.4, 'so' => 1.3, 'absolutely' => 2.0, 'completely' => 1.8,
            'totally' => 1.8, 'utterly' => 1.9, 'highly' => 1.6, 'incredibly' => 1.9
        ];
    }
    
    public function analyze(string $text): array
    {
        $words = $this->tokenize($text);
        $sentimentScore = 0;
        $positiveCount = 0;
        $negativeCount = 0;
        $intensity = 1;
        
        foreach ($words as $word) {
            // Check for intensifiers
            if (isset($this->intensifiers[$word])) {
                $intensity = $this->intensifiers[$word];
                continue;
            }
            
            // Check sentiment
            if (in_array($word, $this->positiveWords)) {
                $sentimentScore += $intensity;
                $positiveCount++;
            } elseif (in_array($word, $this->negativeWords)) {
                $sentimentScore -= $intensity;
                $negativeCount++;
            }
            
            // Reset intensity
            $intensity = 1;
        }
        
        // Normalize score
        $totalWords = count($words);
        $normalizedScore = $totalWords > 0 ? $sentimentScore / sqrt($totalWords) : 0;
        
        // Determine sentiment
        if ($normalizedScore > 0.1) {
            $sentiment = 'positive';
        } elseif ($normalizedScore < -0.1) {
            $sentiment = 'negative';
        } else {
            $sentiment = 'neutral';
        }
        
        // Calculate confidence
        $confidence = min(abs($normalizedScore), 1.0);
        
        return [
            'sentiment' => $sentiment,
            'score' => round($normalizedScore, 3),
            'confidence' => round($confidence, 3),
            'positive_words' => $positiveCount,
            'negative_words' => $negativeCount,
            'total_words' => $totalWords
        ];
    }
    
    public function analyzeBatch(array $texts): array
    {
        $results = [];
        
        foreach ($texts as $text) {
            $results[] = $this->analyze($text);
        }
        
        return $results;
    }
    
    public function getSentimentDistribution(array $texts): array
    {
        $results = $this->analyzeBatch($texts);
        
        $distribution = [
            'positive' => 0,
            'negative' => 0,
            'neutral' => 0
        ];
        
        foreach ($results as $result) {
            $distribution[$result['sentiment']]++;
        }
        
        $total = count($results);
        foreach ($distribution as $sentiment => $count) {
            $distribution[$sentiment] = $total > 0 ? $count / $total : 0;
        }
        
        return $distribution;
    }
    
    private function tokenize(string $text): array
    {
        // Convert to lowercase and extract words
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $words = explode(' ', $text);
        
        // Remove empty strings
        return array_filter($words, fn($word) => !empty($word));
    }
    
    public function addPositiveWords(array $words): void
    {
        $this->positiveWords = array_merge($this->positiveWords, $words);
        $this->positiveWords = array_unique($this->positiveWords);
    }
    
    public function addNegativeWords(array $words): void
    {
        $this->negativeWords = array_merge($this->negativeWords, $words);
        $this->negativeWords = array_unique($this->negativeWords);
    }
    
    public function addIntensifier(string $word, float $intensity): void
    {
        $this->intensifiers[$word] = $intensity;
    }
}

// Anomaly Detection
class AnomalyDetector
{
    private array $data = [];
    private float $threshold = 2.0; // Standard deviations
    private array $statistics = [];
    
    public function train(array $data): void
    {
        $this->data = $data;
        $this->calculateStatistics();
        
        echo "Trained anomaly detector on " . count($data) . " data points\n";
        echo "Mean: " . round($this->statistics['mean'], 3) . "\n";
        echo "Standard Deviation: " . round($this->statistics['std_dev'], 3) . "\n";
        echo "Threshold: ±{$this->threshold} std dev\n\n";
    }
    
    private function calculateStatistics(): void
    {
        if (empty($this->data)) {
            throw new Exception('No data provided for training');
        }
        
        $mean = array_sum($this->data) / count($this->data);
        
        $variance = 0;
        foreach ($this->data as $value) {
            $variance += pow($value - $mean, 2);
        }
        
        $stdDev = sqrt($variance / count($this->data));
        
        $this->statistics = [
            'mean' => $mean,
            'std_dev' => $stdDev,
            'min' => min($this->data),
            'max' => max($this->data)
        ];
    }
    
    public function detect(float $value): bool
    {
        if (empty($this->statistics)) {
            throw new Exception('Detector not trained yet');
        }
        
        $zScore = abs($value - $this->statistics['mean']) / $this->statistics['std_dev'];
        
        return $zScore > $this->threshold;
    }
    
    public function detectBatch(array $values): array
    {
        $anomalies = [];
        
        foreach ($values as $index => $value) {
            if ($this->detect($value)) {
                $anomalies[] = [
                    'index' => $index,
                    'value' => $value,
                    'z_score' => abs($value - $this->statistics['mean']) / $this->statistics['std_dev']
                ];
            }
        }
        
        return $anomalies;
    }
    
    public function getAnomalyScore(float $value): float
    {
        if (empty($this->statistics)) {
            throw new Exception('Detector not trained yet');
        }
        
        return abs($value - $this->statistics['mean']) / $this->statistics['std_dev'];
    }
    
    public function setThreshold(float $threshold): void
    {
        $this->threshold = $threshold;
    }
    
    public function getStatistics(): array
    {
        return $this->statistics;
    }
    
    public function getThreshold(): float
    {
        return $this->threshold;
    }
}

// Practical ML Applications Examples
class PracticalMLApplicationsExamples
{
    public function demonstrateSpamClassifier(): void
    {
        echo "Spam Email Classifier Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $classifier = new SpamEmailClassifier();
        
        // Training data
        $trainingEmails = [
            'Free money now! Click here to claim your prize!',
            'Meeting tomorrow at 2pm to discuss the project',
            'URGENT: Your account will be closed unless you act now',
            'Hi team, please review the attached document',
            'Buy now and get 50% off! Limited time offer',
            'Can we schedule a call next week to discuss the proposal?',
            'Congratulations! You\'ve won a $1000 gift card',
            'The weather is nice today, isn\'t it?'
        ];
        
        $labels = ['spam', 'ham', 'spam', 'ham', 'spam', 'ham', 'spam', 'ham'];
        
        $classifier->train($trainingEmails, $labels);
        
        // Test emails
        echo "\nTesting emails:\n";
        $testEmails = [
            'Get free money now! Limited offer!',
            'Please review the quarterly report',
            'Congratulations! You\'ve won a free iPhone',
            'Let\'s have lunch tomorrow'
        ];
        
        foreach ($testEmails as $email) {
            $result = $classifier->predictWithProbability($email);
            echo "Email: '$email'\n";
            echo "Prediction: {$result['prediction']} (confidence: " . round($result['probability'], 3) . ")\n\n";
        }
        
        // Show top words
        echo "Top spam indicators:\n";
        $topSpamWords = $classifier->getTopSpamWords(5);
        foreach ($topSpamWords as $word => $count) {
            echo "  $word: $count occurrences\n";
        }
        
        echo "\nTop ham indicators:\n";
        $topHamWords = $classifier->getTopHamWords(5);
        foreach ($topHamWords as $word => $count) {
            echo "  $word: $count occurrences\n";
        }
    }
    
    public function demonstrateRecommendationSystem(): void
    {
        echo "\nRecommendation System Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $recommender = new RecommendationSystem();
        
        // Add new user
        $newUserRatings = [
            'item1' => 4,
            'item2' => 2,
            'item3' => 5
        ];
        
        $recommender->addUserRatings('user6', $newUserRatings);
        
        echo "New user ratings:\n";
        foreach ($newUserRatings as $item => $rating) {
            echo "  $item: $rating\n";
        }
        
        // Get recommendations
        echo "\nUser-based recommendations for user6:\n";
        $userBasedRecs = $recommender->getUserBasedRecommendations('user6');
        foreach ($userBasedRecs as $item => $score) {
            echo "  $item: " . round($score, 3) . "\n";
        }
        
        echo "\nItem-based recommendations for user6:\n";
        $itemBasedRecs = $recommender->getItemBasedRecommendations('user6');
        foreach ($itemBasedRecs as $item => $score) {
            echo "  $item: " . round($score, 3) . "\n";
        }
        
        echo "\nHybrid recommendations for user6:\n";
        $hybridRecs = $recommender->getHybridRecommendations('user6');
        foreach ($hybridRecs as $item => $score) {
            echo "  $item: " . round($score, 3) . "\n";
        }
        
        // Show similar users
        echo "\nUsers similar to user6:\n";
        $similarUsers = $recommender->getTopSimilarUsers('user6', 3);
        foreach ($similarUsers as $user => $similarity) {
            echo "  $user: " . round($similarity, 3) . "\n";
        }
    }
    
    public function demonstrateSentimentAnalysis(): void
    {
        echo "\nSentiment Analysis Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $analyzer = new SentimentAnalyzer();
        
        // Sample texts
        $texts = [
            'I absolutely love this product! It\'s amazing and works perfectly.',
            'This is terrible. I hate it and want my money back.',
            'The product is okay, nothing special but not bad either.',
            'Very disappointing experience. The customer service was awful.',
            'Excellent quality and great value for money. Highly recommended!',
            'It works fine, but the design could be better.',
            'Worst purchase ever! Completely useless and overpriced.',
            'Pretty good overall, though shipping took too long.'
        ];
        
        echo "Analyzing sentiments:\n";
        foreach ($texts as $i => $text) {
            $result = $analyzer->analyze($text);
            echo "Text $i: '$text'\n";
            echo "  Sentiment: {$result['sentiment']}\n";
            echo "  Score: {$result['score']}\n";
            echo "  Confidence: {$result['confidence']}\n";
            echo "  Positive words: {$result['positive_words']}\n";
            echo "  Negative words: {$result['negative_words']}\n\n";
        }
        
        // Get distribution
        echo "Sentiment distribution:\n";
        $distribution = $analyzer->getSentimentDistribution($texts);
        foreach ($distribution as $sentiment => $percentage) {
            echo "  $sentiment: " . round($percentage * 100, 1) . "%\n";
        }
        
        // Add custom words
        echo "\nAdding custom words...\n";
        $analyzer->addPositiveWords(['fantastic', 'awesome', 'superb']);
        $analyzer->addNegativeWords(['horrible', 'awful', 'disappointing']);
        echo "Added custom words to vocabulary\n";
        
        // Re-analyze with custom words
        echo "\nRe-analyzing with custom words:\n";
        $customText = 'This is fantastic and awesome, not horrible at all!';
        $result = $analyzer->analyze($customText);
        echo "Text: '$customText'\n";
        echo "  Sentiment: {$result['sentiment']}\n";
        echo "  Score: {$result['score']}\n";
    }
    
    public function demonstrateAnomalyDetection(): void
    {
        echo "\nAnomaly Detection Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $detector = new AnomalyDetector();
        
        // Normal data (e.g., response times in ms)
        $normalData = [
            120, 135, 125, 130, 128, 132, 127, 133, 129, 126,
            131, 134, 122, 136, 129, 130, 127, 135, 128, 132
        ];
        
        $detector->train($normalData);
        
        // Test data including anomalies
        $testData = [
            125, 128, 130,  // Normal values
            250, 15, 450   // Anomalies
        ];
        
        echo "Testing values: " . implode(', ', $testData) . "\n\n";
        
        foreach ($testData as $value) {
            $isAnomaly = $detector->detect($value);
            $score = $detector->getAnomalyScore($value);
            
            echo "Value $value: " . ($isAnomaly ? 'ANOMALY' : 'Normal') . " (score: " . round($score, 2) . ")\n";
        }
        
        // Batch detection
        echo "\nBatch anomaly detection:\n";
        $anomalies = $detector->detectBatch($testData);
        
        if (!empty($anomalies)) {
            echo "Anomalies found:\n";
            foreach ($anomalies as $anomaly) {
                echo "  Index {$anomaly['index']}: Value {$anomaly['value']} (Z-score: " . round($anomaly['z_score'], 2) . ")\n";
            }
        } else {
            echo "No anomalies detected\n";
        }
        
        // Test with different threshold
        echo "\nTesting with different threshold:\n";
        $detector->setThreshold(1.5);
        echo "New threshold: ±1.5 std dev\n";
        
        foreach ([250, 15, 450] as $value) {
            $isAnomaly = $detector->detect($value);
            echo "Value $value: " . ($isAnomaly ? 'ANOMALY' : 'Normal') . "\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nPractical ML Applications Best Practices\n";
        echo str_repeat("-", 45) . "\n";
        
        echo "1. Spam Classification:\n";
        echo "   • Use proper text preprocessing\n";
        echo "   • Implement Laplace smoothing\n";
        echo "   • Update model regularly\n";
        echo "   • Consider feature engineering\n";
        echo "   • Monitor false positives/negatives\n\n";
        
        echo "2. Recommendation Systems:\n";
        echo "   • Use multiple recommendation strategies\n";
        echo "   • Handle cold start problem\n";
        echo "   • Consider scalability\n";
        echo "   • Implement proper evaluation metrics\n";
        echo "   • Use hybrid approaches\n\n";
        
        echo "3. Sentiment Analysis:\n";
        echo "   • Use comprehensive word lists\n";
        echo "   • Handle negation and intensifiers\n";
        echo "   • Consider context and domain\n";
        echo "   • Implement proper tokenization\n";
        echo "   • Use ensemble methods for accuracy\n\n";
        
        echo "4. Anomaly Detection:\n";
        echo "   • Choose appropriate statistical methods\n";
        echo "   • Set proper thresholds\n";
        echo "   • Consider multiple dimensions\n";
        echo "   • Implement real-time detection\n";
        echo "   • Use ensemble methods\n\n";
        
        echo "5. General Guidelines:\n";
        echo "   • Validate models with real data\n";
        echo "   • Monitor model performance\n";
        echo "   • Update models regularly\n";
        echo "   • Document model behavior\n";
        echo "   • Consider ethical implications";
    }
    
    public function runAllExamples(): void
    {
        echo "Practical Machine Learning Applications Examples\n";
        echo str_repeat("=", 45) . "\n";
        
        $this->demonstrateSpamClassifier();
        $this->demonstrateRecommendationSystem();
        $this->demonstrateSentimentAnalysis();
        $this->demonstrateAnomalyDetection();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runPracticalMLApplicationsDemo(): void
{
    $examples = new PracticalMLApplicationsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runPracticalMLApplicationsDemo();
}
?>
