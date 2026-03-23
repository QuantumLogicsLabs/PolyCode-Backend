# Natural Language Processing in Ruby
# Comprehensive guide to NLP techniques and implementations

## 📝 NLP Fundamentals

### 1. NLP Concepts and Terminology

Core natural language processing concepts:

```ruby
class NLPFundamentals
  def self.explain_nlp_concepts
    puts "Natural Language Processing Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Tokenization",
        description: "Breaking text into meaningful units",
        types: ["Word tokenization", "Subword tokenization", "Sentence tokenization"],
        importance: "First step in text processing",
        examples: ["WordPiece", "BERT Tokenizer", "Whitespace tokenizer"]
      },
      {
        concept: "Embeddings",
        description: "Vector representations of text",
        types: ["Word embeddings", "Character embeddings", "Positional embeddings"],
        importance: "Convert text to numerical format",
        examples: ["Word2Vec", "GloVe", "FastText", "BERT embeddings"]
      },
      {
        concept: "Text Preprocessing",
        description: "Cleaning and normalizing text",
        techniques: ["Lowercasing", "Punctuation removal", "Stop word removal", "Stemming", "Lemmatization"],
        purpose: "Standardize text for processing"
      },
      {
        concept: "Language Models",
        description: "Models that predict next words/tokens",
        types: ["Statistical models", "Neural models", "Transformer models"],
        applications: ["Text generation", "Machine translation", "Summarization"]
      },
      {
        concept: "Sequence-to-Sequence",
        description: "Models that map input sequences to output sequences",
        applications: ["Machine translation", "Text summarization", "Chatbots"],
        architecture: "Encoder-decoder with attention"
      },
      {
        concept: "Attention Mechanisms",
        description: "Focus on relevant parts of input",
        types: ["Self-attention", "Cross-attention", "Multi-head attention"],
        benefits: ["Context understanding", "Long-range dependencies"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Importance: #{concept[:importance]}" if concept[:importance]
      puts "  Purpose: #{concept[:purpose]}" if concept[:purpose]
      puts "  Examples: #{concept[:examples].join(', ')}" if concept[:examples]
      puts "  Applications: #{concept[:applications].join(', ')}" if concept[:applications]
      puts "  Architecture: #{concept[:architecture]}" if concept[:architecture]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Techniques: #{concept[:techniques].join(', ')}" if concept[:techniques]
      puts
    end
  end
  
  def self.nlp_tasks
    puts "\nNLP Tasks and Applications:"
    puts "=" * 50
    
    tasks = [
      {
        task: "Text Classification",
        description: "Categorize text into predefined classes",
        examples: ["Spam detection", "Sentiment analysis", "Topic classification"],
        algorithms: ["Naive Bayes", "SVM", "Neural networks", "Transformers"]
      },
      {
        task: "Named Entity Recognition",
        description: "Identify entities in text",
        examples: ["Person names", "Organizations", "Locations", "Dates"],
        algorithms: ["CRF", "BiLSTM-CRF", "Transformers"]
      },
      {
        task: "Part-of-Speech Tagging",
        description: "Assign grammatical categories to words",
        examples: ["Noun", "Verb", "Adjective", "Preposition"],
        algorithms: ["HMM", "CRF", "Neural networks"]
      },
      {
        task: "Machine Translation",
        description: "Translate text between languages",
        examples: ["English to French", "Chinese to English", "Spanish to German"],
        algorithms: ["RNN", "Transformer", "Neural machine translation"]
      },
      {
        task: "Text Summarization",
        description: "Generate concise summaries of longer text",
        examples: ["Article summarization", "Meeting notes", "Document summarization"],
        algorithms: ["Extractive", "Abstractive", "Sequence-to-sequence"]
      },
      {
        task: "Question Answering",
        description: "Answer questions based on given text",
        examples: ["Reading comprehension", "FAQ systems", "Chatbots"],
        algorithms: ["BERT", "T5", "GPT models"]
      }
    ]
    
    tasks.each do |task|
      puts "#{task[:task]}:"
      puts "  Description: #{task[:description]}"
      puts "  Examples: #{task[:examples].join(', ')}"
      puts "  Algorithms: #{task[:algorithms].join(', ')}"
      puts
    end
  end
  
  def self.nlp_challenges
    puts "\nNLP Challenges:"
    puts "=" * 50
    
    challenges = [
      {
        challenge: "Ambiguity",
        description: "Multiple interpretations of text",
        examples: ["Bank (river vs financial)", "Apple (fruit vs company)"],
        solutions: ["Context modeling", "Disambiguation techniques"]
      },
      {
        challenge: "Sarcasm and Irony",
        description: "Text meaning opposite to literal meaning",
        examples: ["Great weather during storm", "Love being stuck in traffic"],
        solutions: ["Sentiment analysis", "Context understanding", "Tone detection"]
      },
      {
        challenge: "Domain Adaptation",
        description: "Models perform differently across domains",
        examples: ["Medical vs legal text", "Social media vs formal writing"],
        solutions: ["Domain adaptation", "Transfer learning", "Fine-tuning"]
      },
      {
        challenge: "Low Resource Languages",
        description: "Limited data for many languages",
        examples: ["Minority languages", "Specialized domains", "Ancient languages"],
        solutions: ["Transfer learning", "Multilingual models", "Data augmentation"]
      },
      {
        challenge: "Long-Range Dependencies",
        description: "Understanding relationships across long distances",
        examples: ["Document coherence", "Story understanding", "Code generation"],
        solutions: ["Attention mechanisms", "Transformer models", "Memory networks"]
      }
    ]
    
    challenges.each do |challenge|
      puts "#{challenge[:challenge]}:"
      puts "  Description: #{challenge[:description]}"
      puts "  Examples: #{challenge[:examples].join(', ')}"
      puts "  Solutions: #{challenge[:solutions].join(', ')}"
      puts
    end
  end
  
  # Run NLP fundamentals
  explain_nlp_concepts
  nlp_tasks
  nlp_challenges
end
```

### 2. Text Preprocessing

Essential text cleaning and preparation:

```ruby
class TextPreprocessor
  def self.tokenize(text)
    puts "Tokenization:"
    puts "=" * 40
    
    # Simple word tokenization
    words = text.downcase.scan(/\b\w+\b/)
    puts "Word tokens: #{words.join(', ')}"
    
    # Sentence tokenization
    sentences = text.split(/[.!?]+/).map(&:strip).reject(&:empty?)
    puts "Sentence tokens: #{sentences.join('. ')}"
    
    # Character tokenization
    chars = text.downcase.chars
    puts "Character tokens: #{chars.join('')}"
    
    [words, sentences, chars]
  end
  
  def self.remove_stop_words(words)
    puts "\nStop Word Removal:"
    puts "=" * 40
    
    # Common English stop words
    stop_words = %w[
      a an the and or but if because as at by for with about against
      between into through during before after above below to from up down in out on off over under again further more
      most other some such no nor not only own same so than too very can will just don should
    ]
    
    filtered_words = words.reject { |word| stop_words.include?(word) }
    
    puts "Original words: #{words.length}"
    puts "Stop words: #{stop_words.length}"
    puts "Filtered words: #{filtered_words.length}"
    puts "Filtered tokens: #{filtered_words.join(', ')}"
    
    filtered_words
  end
  
  def self.stem_and_lemmatize(words)
    puts "\nStemming and Lemmatization:"
    puts "=" * 40
    
    # Simple stemming (Porter stemmer simplified)
    def simple_stem(word)
      return word if word.length <= 3
      
      # Remove common suffixes
      word = word.sub(/ies$/, 'i')
      word = word.sub(/ied$/, 'i')
      word = word.sub(/ing$/, 'i')
      word = word.sub(/s$/, 'i')
      word = word.sub(/ed$/, 'i')
      word = word.sub(/er$/, 'i')
      
      word
    end
    
    # Simple lemmatization (dictionary-based)
    lemmas = {
      'running' => 'run',
      'ran' => 'run',
      'eats' => 'eat',
      'ate' => 'eat',
      'better' => 'good',
      'worse' => 'bad',
      'children' => 'child',
      'mice' => 'mouse',
      'feet' => 'foot',
      'teeth' => 'tooth',
      'geese' => 'goose',
      'cacti' => 'cactus'
    }
    
    stemmed_words = words.map { |word| simple_stem(word) }
    lemmatized_words = words.map { |word| lemmas[word] || word }
    
    puts "Original: #{words.join(', ')}"
    puts "Stemmed: #{stemmed_words.join(', ')}"
    puts "Lemmatized: #{lemmatized_words.join(', ')}"
    
    [stemmed_words, lemmatized_words]
  end
  
  def self.normalize_text(text)
    puts "\nText Normalization:"
    puts "=" * 40
    
    puts "Original: #{text}"
    
    # Lowercase
    normalized = text.downcase
    puts "Lowercase: #{normalized}"
    
    # Remove punctuation
    normalized = normalized.gsub(/[^\w\s]/, '')
    puts "No punctuation: #{normalized}"
    
    # Remove extra whitespace
    normalized = normalized.gsub(/\s+/, ' ').strip
    puts "Normalized: #{normalized}"
    
    normalized
  end
  
  def self.demonstrate_preprocessing
    puts "Text Preprocessing Demonstration:"
    puts "=" * 50
    
    sample_text = "The quick brown fox jumps over the lazy dogs! The dogs were running and jumping."
    
    puts "Sample text: #{sample_text}"
    
    # Normalize text
    normalized = normalize_text(sample_text)
    
    # Tokenize
    words, sentences, chars = tokenize(normalized)
    
    # Remove stop words
    filtered_words = remove_stop_words(words)
    
    # Stem and lemmatize
    stemmed, lemmatized = stem_and_lemmatize(filtered_words)
    
    puts "\nFinal preprocessing pipeline:"
    puts "1. Normalize: #{normalize_text(sample_text)}"
    puts "2. Tokenize: #{filtered_words.join(', ')}"
    puts "3. Remove stop words: #{remove_stop_words(filtered_words).join(', ')}"
    puts "4. Lemmatize: #{lemmatized.join(', ')}"
  end
end
```

## 🤖 Text Classification

### 3. Naive Bayes Classifier

Simple but effective text classifier:

```ruby
class NaiveBayesClassifier
  def initialize
    @classes = {}
    @class_word_counts = {}
    @class_word_totals = {}
    @class_totals = {}
    @vocabulary = Set.new
    @total_documents = 0
  end
  
  def train(documents, labels)
    @total_documents = documents.length
    
    # Count classes
    labels.each do |label|
      @classes[label] = (@classes[label] || 0) + 1
    end
    
    # Count words per class
    documents.each_with_index do |doc, i|
      label = labels[i]
      words = tokenize(doc)
      
      @class_word_counts[label] ||= {}
      @class_word_totals[label] ||= 0
      
      words.each do |word|
        @class_word_counts[label][word] = (@class_word_counts[label][word] || 0) + 1
        @class_word_totals[label] += 1
        @vocabulary.add(word)
      end
    end
    
    # Calculate class totals
    @classes.each do |label, count|
      @class_totals[label] = count.to_f / @total_documents
    end
  end
  
  def classify(document)
    words = tokenize(document)
    word_counts = words.each_with_object(Hash.new(0)) { |word, counts| counts[word] += 1 }
    
    class_scores = {}
    
    @classes.each do |label, _|
      # Start with class prior
      score = Math.log(@class_totals[label])
      
      # Add word likelihoods
      @vocabulary.each do |word|
        word_count = word_counts[word] || 1
        
        # Word probability in this class (with Laplace smoothing)
        word_prob = (@class_word_counts[label][word] || 0) + 1
        total_words = @class_word_totals[label] + @vocabulary.size
        word_likelihood = word_prob.to_f / total_words
        
        # Add log likelihood
        score += word_count * Math.log(word_likelihood)
      end
      
      class_scores[label] = score
    end
    
    # Return class with highest score
    class_scores.max_by { |_, score| score }[0]
  end
  
  def get_class_probabilities(document)
    words = tokenize(document)
    word_counts = words.each_with_object(Hash.new(0)) { |word, counts| counts[word] += 1 }
    
    class_probs = {}
    
    @classes.each do |label, _|
      score = @class_totals[label]
      
      @vocabulary.each do |word|
        word_count = word_counts[word] || 1
        word_prob = (@class_word_counts[label][word] || 0) + 1
        total_words = @class_word_totals[label] + @vocabulary.size
        word_likelihood = word_prob.to_f / total_words
        score *= word_likelihood ** word_count
      end
      
      class_probs[label] = score
    end
    
    # Normalize probabilities
    total_score = class_probs.values.sum
    class_probs.transform_values { |score| score / total_score }
  end
  
  private
  
  def tokenize(text)
    # Simple tokenization
    text.downcase.scan(/\b\w+\b/)
  end
  
  def self.demonstrate_naive_bayes
    puts "Naive Bayes Classifier Demonstration:"
    puts "=" * 50
    
    # Training data
    documents = [
      "This is a great movie, I loved it",
      "Amazing film, highly recommended",
      "Terrible movie, waste of time",
      "I hate this film, it's boring",
      "Good movie, worth watching",
      "Bad acting, poor script",
      "Excellent cinematography and direction",
      "Poor quality, not recommended"
    ]
    
    labels = ["positive", "positive", "negative", "negative", "positive", "negative", "positive", "negative"]
    
    puts "Training data:"
    documents.each_with_index do |doc, i|
      puts "  #{labels[i]}: #{doc}"
    end
    
    # Train classifier
    classifier = NaiveBayesClassifier.new
    classifier.train(documents, labels)
    
    # Test documents
    test_docs = [
      "This movie is fantastic",
      "I really enjoyed this film",
      "Worst movie ever made",
      "Not worth watching"
    ]
    
    puts "\nTest classifications:"
    test_docs.each_with_index do |doc, i|
      prediction = classifier.classify(doc)
      probs = classifier.get_class_probabilities(doc)
      
      puts "Document #{i + 1}: #{doc}"
      puts "  Predicted: #{prediction}"
      puts "  Probabilities: #{probs.map { |label, prob| "#{label}: #{prob.round(3)}" }.join(', ')}"
    end
    
    puts "\nNaive Bayes Features:"
    puts "- Probabilistic classification"
    puts "- Independence assumption"
    puts "- Fast training and prediction"
    puts "- Works well with text data"
  end
end
```

### 4. Sentiment Analysis

Emotion and sentiment detection:

```ruby
class SentimentAnalyzer
  def self.create_sentiment_lexicon
    # Simple sentiment lexicon
    {
      positive: %w[
        good great excellent amazing wonderful fantastic superb outstanding
        brilliant marvelous spectacular delightful pleasant enjoyable
        happy joyful cheerful excited thrilled delighted pleased satisfied
        love like enjoy appreciate value treasure cherish
        beautiful pretty attractive gorgeous stunning handsome
        success achievement victory triumph win
        confident optimistic hopeful positive encouraged
      ],
      negative: %w[
        bad terrible awful horrible dreadful poor mediocre
        hate dislike loathe detest abhor despise
        sad unhappy miserable depressed gloomy somber
        angry furious enraged irritated annoyed frustrated
        failure defeat loss setback disappointment
        ugly repulsive hideous unattractive
        worried anxious nervous stressed fearful scared
        pessimistic doubtful uncertain skeptical
      ],
      neutral: %w[
        okay fine normal average standard typical
        information data fact statement report
        maybe perhaps possibly probably
        neutral objective impartial unbiased
        moderate reasonable acceptable
      ]
    }
  end
  
  def self.sentiment_by_keywords(text)
    lexicon = create_sentiment_lexicon
    words = text.downcase.scan(/\b\w+\b/)
    
    scores = { positive: 0, negative: 0, neutral: 0 }
    
    words.each do |word|
      lexicon.each do |sentiment, word_list|
        scores[sentiment] += 1 if word_list.include?(word)
      end
    end
    
    total = scores.values.sum
    return { sentiment: 'neutral', confidence: 0.5 } if total == 0
    
    # Calculate weighted score
    weighted_score = (scores[:positive] * 1 + scores[:neutral] * 0 + scores[:negative] * -1).to_f / total
    
    sentiment = if weighted_score > 0.1
      'positive'
    elsif weighted_score < -0.1
      'negative'
    else
      'neutral'
    end
    
    confidence = (scores[sentiment.to_sym].to_f / total).round(3)
    
    { sentiment: sentiment, confidence: confidence }
  end
  
  def self.emotion_detection(text)
    emotion_lexicon = {
      joy: %w[happy joyful excited thrilled delighted cheerful],
      sadness: %w[sad depressed miserable gloomy downcast],
      anger: %w[angry furious enraged irritated annoyed],
      fear: %w[scared afraid terrified frightened nervous],
      surprise: %w[surprised amazed shocked astonished],
      disgust: %w[disgusted revolted repulsed sickened]
    }
    
    words = text.downcase.scan(/\b\w+\b/)
    
    emotion_scores = Hash.new(0)
    
    words.each do |word|
      emotion_lexicon.each do |emotion, emotion_words|
        emotion_scores[emotion] += 1 if emotion_words.include?(word)
      end
    end
    
    total = emotion_scores.values.sum
    return { emotion: 'neutral', confidence: 0 } if total == 0
    
    # Find dominant emotion
    dominant_emotion = emotion_scores.max_by { |_, score| score }[0]
    confidence = emotion_scores[dominant_emotion].to_f / total
    
    { emotion: dominant_emotion, confidence: confidence.round(3) }
  end
  
  def self.aspect_based_sentiment(text)
    # Simple aspect-based sentiment analysis
    aspects = {
      'product' => {
        keywords: %w[product item quality feature performance],
        text: text
      },
      'service' => {
        keywords: %w[service support help assistance staff],
        text: text
      },
      'price' => {
        keywords: %w[price cost expensive cheap affordable],
        text: text
      }
    }
    
    results = {}
    
    aspects.each do |aspect, data|
      aspect_text = extract_aspect_sentences(data[:text], data[:keywords])
      
      if aspect_text.any?
        sentiment = sentiment_by_keywords(aspect_text.join(' '))
        results[aspect] = sentiment
      end
    end
    
    results
  end
  
  def self.extract_aspect_sentences(text, keywords)
    sentences = text.split(/[.!?]+/).map(&:strip).reject(&:empty?)
    
    sentences.select do |sentence|
      keywords.any? { |keyword| sentence.include?(keyword) }
    end
  end
  
  def self.demonstrate_sentiment_analysis
    puts "Sentiment Analysis Demonstration:"
    puts "=" * 50
    
    # Sample texts
    texts = [
      "This product is absolutely amazing! I love the quality and performance.",
      "The customer service was terrible and the staff was unhelpful.",
      "The price is reasonable but the quality could be better.",
      "I'm not sure about this. It's okay, I guess."
    ]
    
    texts.each_with_index do |text, i|
      puts "\nText #{i + 1}: #{text}"
      
      # Overall sentiment
      overall_sentiment = sentiment_by_keywords(text)
      puts "Overall sentiment: #{overall_sentiment[:sentiment]} (confidence: #{overall_sentiment[:confidence]})"
      
      # Emotion detection
      emotion = emotion_detection(text)
      puts "Emotion: #{emotion[:emotion]} (confidence: #{emotion[:confidence]})"
      
      # Aspect-based sentiment
      aspects = aspect_based_sentiment(text)
      if aspects.any?
        puts "Aspect-based sentiment:"
        aspects.each do |aspect, sentiment|
          puts "  #{aspect}: #{sentiment[:sentiment]} (confidence: #{sentiment[:confidence]})"
        end
      end
    end
    
    puts "\nSentiment Analysis Features:"
    puts "- Keyword-based sentiment analysis"
    puts "- Emotion detection"
    puts "- Aspect-based sentiment"
    puts "- Confidence scoring"
    puts "- Multi-dimensional analysis"
  end
end
```

## 🔍 Information Extraction

### 5. Named Entity Recognition

Identify entities in text:

```ruby
class NamedEntityRecognizer
  def initialize
    @entity_patterns = {
      person: [
        /\b[A-Z][a-z]+ [A-Z][a-z]+/,  # First Last
        /\b(?:Mr|Mrs|Ms|Dr|Prof)\. [A-Z][a-z]+/,  # Title + Name
        /\b[A-Z][a-z]+(?:-[A-Z][a-z]+)+/   # Hyphenated names
      ],
      organization: [
        /\b[A-Z][a-z]+ (?:Inc|Corp|LLC|Ltd|Co|Company)\b/,
        /\b(?:Apple|Google|Microsoft|Amazon|Facebook|Twitter)\b/,
        /\b(?:University|College|Institute) of [A-Z][a-z]+\b/
      ],
      location: [
        /\b[A-Z][a-z]+(?:, [A-Z][a-z]+)+/,  # City, State
        /\b(?:United States|United Kingdom|New York|Los Angeles)\b/,
        /\b[A-Z][a-z]+ (?:Street|Avenue|Boulevard|Road)\b/
      ],
      date: [
        /\b(?:January|February|March|April|May|June|July|August|September|October|November|December) \d{1,2}, \d{4}\b/,
        /\b\d{1,2}\/\d{1,2}\/\d{4}\b/,
        /\b\d{4}-\d{2}-\d{2}\b/
      ],
      email: [
        /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/
      ],
      phone: [
        /\b(?:\+?1[-.\s]?)?\(?:(?:\(?(\d{3})\)[-.\s]?)?(\d{3})[-.\s]?(\d{4})|\(\d{3})[-.\s]?(\d{3})[-.\s]?(\d{4}))\b/
      ],
      money: [
        /\$\d+(?:\.\d{2})?/,
        /\b\d+(?:,\d{3})*(?:\.\d{2})? dollars?\b/,
        /\b(?:USD|EUR|GBP|JPY)\s*\d+(?:\.\d{2})?\b/
      ]
    }
  end
  
  def extract_entities(text)
    entities = []
    
    @entity_patterns.each do |entity_type, patterns|
      patterns.each do |pattern|
        text.scan(pattern) do |match|
          entity = if match.is_a?(Array)
            match.join(' ')
          else
            match
          end
          
          entities << {
            text: entity,
            type: entity_type,
            position: text.index(entity)
          }
        end
      end
    end
    
    # Remove overlapping entities (keep longest match)
    entities = remove_overlapping_entities(entities)
    
    entities
  end
  
  def extract_entities_with_context(text, context_window = 5)
    entities = extract_entities(text)
    
    entities.each do |entity|
      start_pos = [entity[:position] - context_window, 0].max
      end_pos = [entity[:position] + entity[:text].length + context_window, text.length].min
      
      entity[:context] = text[start_pos...end_pos]
    end
    
    entities
  end
  
  def self.demonstrate_ner
    puts "Named Entity Recognition Demonstration:"
    puts "=" * 50
    
    sample_text = "John Smith works at Google Inc. in New York. His email is john.smith@google.com and phone number is (555) 123-4567. He was born on January 15, 1985 and earns $75,000 per year."
    
    puts "Sample text: #{sample_text}"
    
    recognizer = NamedEntityRecognizer.new
    entities = recognizer.extract_entities_with_context(sample_text)
    
    puts "\nExtracted entities:"
    entities.each do |entity|
      puts "  #{entity[:type].capitalize}: #{entity[:text]}"
      puts "    Position: #{entity[:position]}"
      puts "    Context: #{entity[:context]}"
      puts
    end
    
    puts "Entity Types Found:"
    entity_types = entities.map { |e| e[:type] }.uniq
    entity_types.each do |type|
      count = entities.count { |e| e[:type] == type }
      puts "  #{type.capitalize}: #{count} entities"
    end
    
    puts "\nNER Features:"
    puts "- Pattern-based recognition"
    puts "- Context extraction"
    puts "- Multiple entity types"
    puts "- Position tracking"
  end
  
  private
  
  def remove_overlapping_entities(entities)
    # Sort by position and length (longer first)
    entities.sort_by { |e| [e[:position], -e[:text].length] }
    
    selected = []
    used_positions = []
    
    entities.each do |entity|
      entity_range = entity[:position]...(entity[:position] + entity[:text].length)
      
      # Check if this entity overlaps with any selected entity
      overlaps = selected.any? do |selected|
        selected_range = selected[:position]...(selected[:position] + selected[:text].length)
        !(entity_range & selected_range).empty?
      end
      
      unless overlaps
        selected << entity
        used_positions.concat(entity_range.to_a)
      end
    end
    
    selected
  end
end
```

### 6. Text Summarization

Generate concise summaries:

```ruby
class TextSummarizer
  def self.extractive_summarization(text, num_sentences = 3)
    sentences = text.split(/[.!?]+/).map(&:strip).reject(&:empty?)
    
    return text if sentences.length <= num_sentences
    
    # Calculate sentence scores based on position and length
    sentence_scores = sentences.map.with_index do |sentence, i|
      position_score = 1.0 / (i + 1)  # Earlier sentences get higher score
      length_score = [sentence.length / 100.0, 1.0].min  # Normalize length
      word_count = sentence.split(/\s+/).length
      
      # Calculate word frequency score
      words = sentence.downcase.scan(/\b\w+\b/)
      word_freq_score = words.uniq.length.to_f / words.length
      
      # Combine scores
      total_score = position_score * 0.3 + length_score * 0.3 + word_freq_score * 0.4
      
      { sentence: sentence, score: total_score, index: i }
    end
    
    # Select top sentences
    selected_scores = sentence_scores.sort_by { |s| -s[:score] }.first(num_sentences)
    
    # Sort by original position
    summary_sentences = selected_scores.sort_by { |s| s[:index] }.map { |s| s[:sentence] }
    
    summary_sentences.join('. ')
  end
  
  def self.abstractive_summarization(text, max_length = 100)
    # Simplified abstractive summarization
    sentences = text.split(/[.!?]+/).map(&:strip).reject(&:empty?)
    
    # Extract key phrases (simplified)
    words = text.downcase.scan(/\b\w+\b/)
    word_freq = words.each_with_object(Hash.new(0)) { |word, freq| freq[word] += 1 }
    
    # Select important words (frequency-based)
    important_words = word_freq.select { |_, freq| freq > 1 }.keys
    
    # Generate summary by selecting sentences with most important words
    summary = []
    current_length = 0
    
    sentences.each do |sentence|
      sentence_words = sentence.downcase.scan(/\b\w+\b/)
      importance = sentence_words.count { |word| important_words.include?(word) }
      
      if current_length + sentence.length <= max_length && importance > 0
        summary << sentence
        current_length += sentence.length + 1  # +1 for period
      end
      
      break if current_length >= max_length
    end
    
    summary.join('. ')
  end
  
  def self.keyword_based_summarization(text, keywords, num_sentences = 3)
    sentences = text.split(/[.!?]+/).map(&:strip).reject(&:empty?)
    
    # Score sentences based on keyword presence
    sentence_scores = sentences.map.with_index do |sentence, i|
      sentence_words = sentence.downcase.scan(/\b\w+\b/)
      keyword_count = sentence_words.count { |word| keywords.include?(word) }
      
      # Score based on keyword density
      density = keyword_count.to_f / [sentence_words.length, 1].max
      
      # Position score (earlier sentences get higher score)
      position_score = 1.0 / (i + 1)
      
      total_score = density * 0.7 + position_score * 0.3
      
      { sentence: sentence, score: total_score, index: i }
    end
    
    # Select sentences with highest scores
    selected_scores = sentence_scores.sort_by { |s| -s[:score] }.first(num_sentences)
    
    # Sort by original position
    summary_sentences = selected_scores.sort_by { |s| s[:index] }.map { |s| s[:sentence] }
    
    summary_sentences.join('. ')
  end
  
  def self.demonstrate_summarization
    puts "Text Summarization Demonstration:"
    puts "=" * 50
    
    sample_text = "Natural language processing is a subfield of linguistics, computer science, and artificial intelligence concerned with the interactions between computers and human language. It focuses on how to program computers to process and analyze large amounts of natural language data. The result is a computer capable of understanding the contents of documents, including the contextual nuances within them. The technology can then accurately extract information and insights contained in the documents as well as categorize and organize the documents themselves. Challenges in NLP frequently involve speech recognition, natural language understanding, and natural language generation."
    
    puts "Original text:"
    puts sample_text
    puts "Length: #{sample_text.length} characters"
    
    # Extractive summarization
    extractive_summary = extractive_summarization(sample_text, 3)
    puts "\nExtractive summary (3 sentences):"
    puts extractive_summary
    puts "Length: #{extractive_summary.length} characters"
    
    # Abstractive summarization
    abstractive_summary = abstractive_summarization(sample_text, 150)
    puts "\nAbstractive summary (max 150 chars):"
    puts abstractive_summary
    puts "Length: #{abstractive_summary.length} characters"
    
    # Keyword-based summarization
    keywords = %w[nlp computer language text processing]
    keyword_summary = keyword_based_summarization(sample_text, keywords, 2)
    puts "\nKeyword-based summary (keywords: #{keywords.join(', ')}):"
    puts keyword_summary
    puts "Length: #{keyword_summary.length} characters"
    
    puts "\nSummarization Features:"
    puts "- Extractive summarization (sentence selection)"
    puts "- Abstractive summarization (content generation)"
    puts "- Keyword-based summarization (focus on key terms)"
    puts "- Length control and position weighting"
  end
end
```

## 🤖 Advanced NLP

### 7. Language Models

Simple language model implementation:

```ruby
class LanguageModel
  def initialize(vocab_size, embedding_dim, hidden_size)
    @vocab_size = vocab_size
    @embedding_dim = embedding_dim
    @hidden_size = hidden_size
    
    # Initialize weights
    @embeddings = Array.new(vocab_size) { Array.new(embedding_dim) { rand * 0.1 - 0.05 } }
    @w_hh = Array.new(hidden_size) { Array.new(hidden_size) { rand * 0.1 - 0.05 } }
    @w_xh = Array.new(hidden_size) { Array.new(embedding_dim) { rand * 0.1 - 0.05 } }
    @w_hy = Array.new(vocab_size) { Array.new(hidden_size) { rand * 0.1 - 0.05 } }
    
    # Biases
    @b_h = Array.new(hidden_size, 0)
    @b_y = Array.new(vocab_size, 0)
    
    # Vocabulary
    @vocab = (0...vocab_size).map { |i| "word_#{i}" }
    @word_to_id = @vocab.each_with_index.to_h
  end
  
  def forward_sequence(sequence)
    hidden_state = Array.new(@hidden_size, 0)
    outputs = []
    
    sequence.each do |token_id|
      # Embedding lookup
      embedding = @embeddings[token_id]
      
      # RNN forward pass
      new_hidden = Array.new(@hidden_size) do |i|
        xh = embedding.zip(@w_xh[i]).sum { |x, w| x * w }
        hh = hidden_state.zip(@w_hh[i]).sum { |h, w| h * w }
        Math.tanh(xh + hh + @b_h[i])
      end
      
      # Output layer
      output = Array.new(@vocab_size) do |i|
        hy = new_hidden.zip(@w_hy[i]).sum { |h, w| h * w }
        Math.exp(hy + @b_y[i])
      end
      
      # Softmax
      total = output.sum
      normalized_output = output.map { |val| val / total }
      
      outputs << normalized_output
      hidden_state = new_hidden
    end
    
    outputs
  end
  
  def train_step(sequence, target_sequence, learning_rate = 0.01)
    # Forward pass
    outputs = forward_sequence(sequence)
    
    # Calculate loss and backward pass (simplified)
    total_loss = 0
    
    sequence.each_with_index do |token_id, t|
      target_id = target_sequence[t]
      
      # Cross-entropy loss
      loss = -Math.log(outputs[t][target_id] + 1e-15)
      total_loss += loss
      
      # Backward pass (simplified gradient calculation)
      output_gradient = outputs[t].dup
      output_gradient[target_id] -= 1
      
      # Update weights (simplified)
      @w_hy.each_with_index do |weights_row, i|
        weights_row.each_with_index do |weight, j|
          gradient = output_gradient[i] * hidden_state[j]
          weights_row[j] -= learning_rate * gradient
        end
        @b_y[i] -= learning_rate * output_gradient[i]
      end
    end
    
    total_loss / sequence.length
  end
  
  def generate_sequence(start_sequence, max_length = 10, temperature = 1.0)
    current_sequence = start_sequence.dup
    hidden_state = Array.new(@hidden_size, 0)
    
    max_length.times do
      # Forward pass
      embedding = @embeddings[current_sequence.last]
      new_hidden = Array.new(@hidden_size) do |i|
        xh = embedding.zip(@w_xh[i]).sum { |x, w| x * w }
        hh = hidden_state.zip(@w_hh[i]).sum { |h, w| h * w }
        Math.tanh(xh + hh + @b_h[i])
      end
      
      # Output layer
      output = Array.new(@vocab_size) do |i|
        hy = new_hidden.zip(@w_hy[i]).sum { |h, w| h * w }
        Math.exp(hy + @b_y[i])
      end
      
      # Softmax with temperature
      total = output.sum
      probabilities = output.map { |val| Math.exp(val / temperature) / total }
      
      # Sample next token
      cumulative = []
      probabilities.each_with_index do |prob, i|
        cumulative << (cumulative.last || 0) + prob
      end
      
      random_val = rand
      next_token_id = cumulative.find_index { |cum| cum >= random_val }
      
      current_sequence << next_token_id
      hidden_state = new_hidden
      
      # Stop if end token
      break if next_token_id == @vocab_size - 1
    end
    
    current_sequence.map { |id| @vocab[id] }
  end
  
  def self.demonstrate_language_model
    puts "Language Model Demonstration:"
    puts "=" * 50
    
    # Create language model
    model = LanguageModel.new(100, 50, 32)
    
    # Generate sample training data
    puts "Generating training data..."
    training_data = 50.times.map do
      5.times.map { rand(95) }  # Exclude end token
    end
    
    target_data = training_data.map do |sequence|
      sequence[1..-1] + [99]  # Add end token
    end
    
    puts "Training sequences: #{training_data.length}"
    puts "Vocabulary size: #{model.vocab_size}"
    
    # Train model
    puts "\nTraining language model..."
    
    10.times do |epoch|
      total_loss = 0
      
      training_data.each_with_index do |sequence, i|
        target = target_data[i]
        loss = model.train_step(sequence, target, 0.01)
        total_loss += loss
      end
      
      avg_loss = total_loss / training_data.length
      puts "Epoch #{epoch + 1}: Loss = #{avg_loss.round(4)}"
    end
    
    # Generate text
    puts "\nText generation:"
    start_sequence = [1, 5, 10]  # word_1, word_5, word_10
    generated = model.generate_sequence(start_sequence, 8, 0.8)
    
    puts "Start sequence: #{start_sequence.map { |id| model.vocab[id] }.join(' ')}"
    puts "Generated: #{generated.join(' ')}"
    
    # Generate with different temperatures
    puts "\nTemperature effects:"
    [0.5, 1.0, 1.5, 2.0].each do |temp|
      generated = model.generate_sequence(start_sequence, 5, temp)
      puts "Temperature #{temp}: #{generated.join(' ')}"
    end
    
    puts "\nLanguage Model Features:"
    puts "- Recurrent neural network architecture"
    puts "- Embedding layer for word representations"
    "- Softmax probability distribution"
    "- Temperature-controlled generation"
    "- Next word prediction"
  end
end
```

## 🎯 Practical NLP Applications

### 8. Text Similarity and Clustering

Text similarity analysis:

```ruby
class TextSimilarity
  def self.cosine_similarity(text1, text2)
    # Extract features (word frequencies)
    words1 = text1.downcase.scan(/\b\w+\b/)
    words2 = text2.downcase.scan(/\b\w+\b/)
    
    # Create vocabulary
    vocab = (words1 + words2).uniq
    vocab_size = vocab.size
    
    # Create frequency vectors
    vector1 = Array.new(vocab_size, 0)
    vector2 = Array.new(vocab_size, 0)
    
    vocab.each_with_index do |word, i|
      vector1[i] = words1.count(word)
      vector2[i] = words2.count(word)
    end
    
    # Calculate cosine similarity
    dot_product = vector1.zip(vector2).sum { |a, b| a * b }
    magnitude1 = Math.sqrt(vector1.sum { |x| x ** 2 })
    magnitude2 = Math.sqrt(vector2.sum { |x| x ** 2 } }
    
    return 0 if magnitude1 == 0 || magnitude2 == 0
    
    dot_product / (magnitude1 * magnitude2)
  end
  
  def self.jaccard_similarity(text1, text2)
    set1 = Set.new(text1.downcase.scan(/\b\w+\b/))
    set2 = Set.new(text2.downcase.scan(/\b\w+\b/))
    
    intersection = set1.intersection(set2)
    union = set1.union(set2)
    
    return 1.0 if union.empty?
    
    intersection.size.to_f / union.size
  end
  
  def self.levenshtein_distance(text1, text2)
    words1 = text1.downcase.scan(/\b\w+\b/)
    words2 = text2.downcase.scan(/\b\w+\b/)
    
    matrix = Array.new(words1.length + 1) { Array.new(words2.length + 1, 0) }
    
    # Initialize first row and column
    (0..words2.length).each { |j| matrix[0][j] = j }
    (0..words1.length).each { |i| matrix[i][0] = i }
    
    # Fill matrix
    (1..words1.length).each do |i|
      (1..words2.length).each do |j|
        if words1[i-1] == words2[j-1]
          matrix[i][j] = matrix[i-1][j-1]
        else
          matrix[i][j] = [
            matrix[i-1][j] + 1,    # deletion
            matrix[i][j-1] + 1,    # insertion
            matrix[i-1][j-1] + 1  # substitution
          ].min
        end
      end
    end
    
    matrix[words1.length][words2.length]
  end
  
  def self.text_clustering(texts, num_clusters = 3)
    # Simple k-means clustering based on similarity
    return [] if texts.empty?
    
    # Calculate similarity matrix
    similarity_matrix = texts.map do |text1|
      texts.map do |text2|
        cosine_similarity(text1, text2)
      end
    end
    
    # Initialize clusters (simplified)
    clusters = (0...num_clusters).map { |i| [i] } }
    
    # Assign each text to closest cluster
    assignments = texts.map.with_index do |text, i|
      cluster_scores = clusters.map do |cluster|
        cluster_texts = clusters.map { |c| texts[c[0]] if c.any? }
        
        if cluster_texts.any?
          cluster_scores.map { |cluster_idx| similarity_matrix[i][cluster_idx] }.sum / cluster_texts.length
        else
          0
        end
      end
      
      best_cluster = cluster_scores.index(cluster_scores.max)
      best_cluster
    end
    
    # Group texts by cluster
    clustered_texts = Hash.new
    assignments.each_with_index do |cluster_id, i|
      clustered_texts[cluster_id] ||= []
      clustered_texts[cluster_id] << texts[i]
    end
    
    clustered_texts
  end
  
  def self.demonstrate_similarity
    puts "Text Similarity Analysis:"
    puts "=" * 50
    
    # Sample texts
    texts = [
      "Machine learning is a subfield of artificial intelligence",
      "Deep learning uses neural networks for pattern recognition",
      "Natural language processing helps computers understand text",
      "Computer vision enables machines to interpret visual information",
      "Data science involves extracting insights from data"
    ]
    
    puts "Sample texts:"
    texts.each_with_index do |text, i|
      puts "  #{i + 1}: #{text}"
    end
    
    # Calculate similarity matrix
    puts "\nSimilarity Matrix (Cosine Similarity):"
    puts "-" * 50
    
    texts.each_with_index do |text1, i|
      similarity_row = texts.map.with_index do |text2, j|
        similarity = cosine_similarity(text1, text2)
        similarity.round(3)
      end
      puts "#{i + 1}: #{similarity_row}"
    end
    
    # Compare different similarity measures
    puts "\nSimilarity Measures Comparison:"
    puts "-" * 50
    
    text1, text2 = texts[0], texts[1]
    
    cosine_sim = cosine_similarity(text1, text2)
    jaccard_sim = jaccard_similarity(text1, text2)
    levenshtein_dist = levenshtein_distance(text1, text2)
    
    puts "Comparing: '#{text1}' vs '#{text2}'"
    puts "Cosine similarity: #{cosine_sim.round(4)}"
    puts "Jaccard similarity: #{jaccard_sim.round(4)}"
    puts "Levenshtein distance: #{levenshtein_dist}"
    
    # Text clustering
    puts "\nText Clustering (3 clusters):"
    puts "-" * 50
    
    clusters = text_clustering(texts, 3)
    
    clusters.each_with_index do |texts_in_cluster, cluster_id|
      puts "Cluster #{cluster_id + 1}: #{texts_in_cluster.length} texts"
      texts_in_cluster.each_with_index do |text, i|
        puts "  #{i + 1}. #{text}"
      end
      puts
    end
    
    puts "Similarity Analysis Features:"
    puts "- Cosine similarity for semantic similarity"
    puts "- Jaccard similarity for word overlap"
    puts "- Levenshtein distance for edit distance"
    puts "- K-means clustering for text grouping"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Text Preprocessing**: Implement tokenization and cleaning
2. **Naive Bayes**: Build text classifier
3. **Sentiment Analysis**: Create sentiment analyzer
4. **NER**: Implement entity recognition

### Intermediate Exercises

1. **Text Summarization**: Build summarization system
2. **Language Model**: Create simple language model
3. **Text Similarity**: Implement similarity measures
4. **Text Clustering**: Group similar texts

### Advanced Exercises

1. **Advanced NLP**: Implement transformer components
2. **Sequence Models**: Build complex sequence models
    3. **Real Applications**: Apply to real datasets
    4. **Performance**: Optimize NLP models

---

## 🎯 Summary

Natural Language Processing in Ruby provides:

- **NLP Fundamentals** - Core concepts and terminology
- **Text Preprocessing** - Tokenization and cleaning techniques
- **Text Classification** - Naive Bayes and sentiment analysis
- **Information Extraction** - Named entity recognition
- **Text Summarization** - Extractive and abstractive methods
- **Language Models** - Simple neural language models
- **Practical Applications** - Similarity and clustering

Master these NLP techniques to process and understand human language!
