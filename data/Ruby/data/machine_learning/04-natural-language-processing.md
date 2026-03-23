# Natural Language Processing in Ruby
# Comprehensive guide to NLP techniques and implementations

## 📝 NLP Fundamentals

### 1. NLP Concepts and Foundations

Core natural language processing principles:

```ruby
class NLPFundamentals
  def self.explain_nlp_concepts
    puts "Natural Language Processing Fundamentals:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Natural Language Processing",
        description: "Computational understanding and generation of human language",
        domains: ["Text processing", "Speech processing", "Understanding", "Generation"],
        challenges: ["Ambiguity", "Context", "Variability", "Cultural differences"],
        applications: ["Machine translation", "Chatbots", "Sentiment analysis", "Information retrieval"]
      },
      {
        concept: "Text Preprocessing",
        description: "Cleaning and preparing text for analysis",
        steps: ["Tokenization", "Normalization", "Stop word removal", "Stemming/Lemmatization"],
        techniques: ["Regular expressions", "String manipulation", "Unicode handling", "Encoding"],
        importance: ["Data quality", "Feature extraction", "Model performance", "Consistency"]
      },
      {
        concept: "Tokenization",
        description: "Breaking text into meaningful units",
        types: ["Word tokenization", "Sentence tokenization", "Subword tokenization"],
        methods: ["Rule-based", "Statistical", "Neural", "Hybrid"],
        challenges: ["Language-specific", "Ambiguity", "Punctuation", "Special characters"]
      },
      {
        concept: "Text Representation",
        description: "Converting text to numerical representations",
        methods: ["Bag of words", "TF-IDF", "Word embeddings", "Contextual embeddings"],
        features: ["Sparse vs dense", "Fixed vs variable length", "Pretrained vs learned"],
        applications: ["Classification", "Similarity", "Clustering", "Generation"]
      },
      {
        concept: "Language Models",
        description: "Statistical models of language",
        types: ["N-gram", "Neural", "Transformer", "Large language models"],
        training: ["Corpus-based", "Self-supervised", "Transfer learning", "Fine-tuning"],
        capabilities: ["Text generation", "Understanding", "Translation", "Summarization"]
      },
      {
        concept: "Text Classification",
        description: "Categorizing text into predefined classes",
        algorithms: ["Naive Bayes", "SVM", "Neural networks", "Transformers"],
        applications: ["Spam detection", "Sentiment analysis", "Topic classification", "Intent detection"],
        evaluation: ["Accuracy", "Precision", "Recall", "F1-score"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Domains: #{concept[:domains].join(', ')}" if concept[:domains]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Applications: #{concept[:applications].join(', ')}" if concept[:applications]
      puts "  Steps: #{concept[:steps].join(', ')}" if concept[:steps]
      puts "  Techniques: #{concept[:techniques].join(', ')}" if concept[:techniques]
      puts "  Importance: #{concept[:importance].join(', ')}" if concept[:importance]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Methods: #{concept[:methods].join(', ')}" if concept[:methods]
      puts "  Features: #{concept[:features].join(', ')}" if concept[:features]
      puts "  Training: #{concept[:training].join(', ')}" if concept[:training]
      puts "  Capabilities: #{concept[:capabilities].join(', ')}" if concept[:capabilities]
      puts "  Algorithms: #{concept[:algorithms].join(', ')}" if concept[:algorithms]
      puts "  Evaluation: #{concept[:evaluation].join(', ')}" if concept[:evaluation]
      puts
    end
  end
  
  def self.nlp_pipeline
    puts "\nNLP Pipeline:"
    puts "=" * 50
    
    pipeline = [
      {
        stage: "1. Data Collection",
        description: "Gathering text data from various sources",
        sources: ["Documents", "Web pages", "Social media", "Books", "Articles"],
        considerations: ["Data quality", "Copyright", "Privacy", "Volume", "Diversity"],
        preprocessing: ["Deduplication", "Quality filtering", "Language detection", "Format normalization"]
      },
      {
        stage: "2. Text Preprocessing",
        description: "Cleaning and preparing text for analysis",
        operations: ["Tokenization", "Normalization", "Stop word removal", "Stemming/Lemmatization"],
        tools: ["Regular expressions", "String libraries", "Unicode handling", "Language-specific tools"],
        output: ["Clean tokens", "Normalized text", "Reduced vocabulary", "Consistent format"]
      },
      {
        stage: "3. Feature Extraction",
        description: "Extracting meaningful features from text",
        methods: ["Bag of words", "TF-IDF", "N-grams", "Word embeddings", "Contextual embeddings"],
        representations: ["Sparse vectors", "Dense vectors", "Matrices", "Tensors"],
        considerations: ["Dimensionality", "Sparsity", "Semantic meaning", "Context preservation"]
      },
      {
        stage: "4. Model Training",
        description: "Training machine learning models on text features",
        algorithms: ["Traditional ML", "Neural networks", "Transformers", "Ensemble methods"],
        training: ["Supervised", "Unsupervised", "Semi-supervised", "Self-supervised"],
        optimization: ["Hyperparameter tuning", "Cross-validation", "Regularization", "Early stopping"]
      },
      {
        stage: "5. Evaluation",
        description: "Assessing model performance",
        metrics: ["Accuracy", "Precision", "Recall", "F1-score", "BLEU", "ROUGE"],
        validation: ["Hold-out", "Cross-validation", "Bootstrap", "Statistical tests"],
        analysis: ["Error analysis", "Feature importance", "Learning curves", "Confusion matrices"]
      },
      {
        stage: "6. Deployment",
        description: "Deploying models for production use",
        considerations: ["Performance", "Scalability", "Monitoring", "Maintenance"],
        deployment: ["API", "Batch processing", "Real-time", "Edge deployment"],
        monitoring: ["Performance metrics", "Data drift", "Model drift", "User feedback"]
      }
    ]
    
    pipeline.each do |stage|
      puts "#{stage[:stage]}: #{stage[:description]}"
      puts "  Sources: #{stage[:sources].join(', ')}" if stage[:sources]
      puts "  Considerations: #{stage[:considerations].join(', ')}" if stage[:considerations]
      puts "  Preprocessing: #{stage[:preprocessing].join(', ')}" if stage[:preprocessing]
      puts "  Operations: #{stage[:operations].join(', ')}" if stage[:operations]
      puts "  Tools: #{stage[:tools].join(', ')}" if stage[:tools]
      puts "  Output: #{stage[:output].join(', ')}" if stage[:output]
      puts "  Methods: #{stage[:methods].join(', ')}" if stage[:methods]
      puts "  Representations: #{stage[:representations].join(', ')}" if stage[:representations]
      puts "  Algorithms: #{stage[:algorithms].join(', ')}" if stage[:algorithms]
      puts "  Training: #{stage[:training].join(', ')}" if stage[:training]
      puts "  Optimization: #{stage[:optimization].join(', ')}" if stage[:optimization]
      puts "  Metrics: #{stage[:metrics].join(', ')}" if stage[:metrics]
      puts "  Validation: #{stage[:validation].join(', ')}" if stage[:validation]
      puts "  Analysis: #{stage[:analysis].join(', ')}" if stage[:analysis]
      puts "  Deployment: #{stage[:deployment].join(', ')}" if stage[:deployment]
      puts "  Monitoring: #{stage[:monitoring].join(', ')}" if stage[:monitoring]
      puts
    end
  end
  
  def self.nlp_applications
    puts "\nNLP Applications:"
    puts "=" * 50
    
    applications = [
      {
        domain: "Machine Translation",
        description: "Automatic translation between languages",
        approaches: ["Rule-based", "Statistical", "Neural", "Transformer-based"],
        challenges: ["Idioms", "Cultural context", "Rare languages", "Domain adaptation"],
        evaluation: ["BLEU", "METEOR", "TER", "Human evaluation"]
      },
      {
        domain: "Sentiment Analysis",
        description: "Determining emotional tone of text",
        levels: ["Document-level", "Sentence-level", "Aspect-based", "Emotion detection"],
        methods: ["Lexicon-based", "Machine learning", "Deep learning", "Hybrid"],
        applications: ["Customer feedback", "Social media", "Product reviews", "Market research"]
      },
      {
        domain: "Text Summarization",
        description: "Creating concise summaries of longer texts",
        types: ["Extractive", "Abstractive", "Hybrid", "Query-based"],
        evaluation: ["ROUGE", "Human evaluation", "Readability", "Informativeness"],
        applications: ["News articles", "Research papers", "Meeting notes", "Legal documents"]
      },
      {
        domain: "Question Answering",
        description: "Answering questions based on text",
        types: ["Extractive", "Generative", "Open-domain", "Closed-domain"],
        approaches: ["Retrieval-based", "Neural", "Hybrid", "Knowledge graph"],
        applications: ["Chatbots", "Search engines", "Virtual assistants", "Help systems"]
      },
      {
        domain: "Named Entity Recognition",
        description: "Identifying and classifying named entities",
        entities: ["Persons", "Organizations", "Locations", "Dates", "Products"],
        methods: ["Rule-based", "Statistical", "Neural", "Transformer"],
        applications: ["Information extraction", "Knowledge graphs", "Document analysis", "Search"]
      },
      {
        domain: "Text Generation",
        description: "Generating human-like text",
        types: ["Conditional", "Unconditional", "Controlled", "Interactive"],
        models: ["Language models", "Transformers", "GANs", "VAEs"],
        applications: ["Chatbots", "Content creation", "Code generation", "Creative writing"]
      }
    ]
    
    applications.each do |app|
      puts "#{app[:domain]}:"
      puts "  Description: #{app[:description]}"
      puts "  Approaches: #{app[:approaches].join(', ')}" if app[:approaches]
      puts "  Challenges: #{app[:challenges].join(', ')}" if app[:challenges]
      puts "  Evaluation: #{app[:evaluation].join(', ')}" if app[:evaluation]
      puts "  Levels: #{app[:levels].join(', ')}" if app[:levels]
      puts "  Methods: #{app[:methods].join(', ')}" if app[:methods]
      puts "  Applications: #{app[:applications].join(', ')}" if app[:applications]
      puts "  Types: #{app[:types].join(', ')}" if app[:types]
      puts "  Entities: #{app[:entities].join(', ')}" if app[:entities]
      puts "  Models: #{app[:models].join(', ')}" if app[:models]
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
        types: ["Lexical", "Syntactic", "Semantic", "Pragmatic"],
        examples: ["Bank (river vs financial)", "Time flies", "I saw a man with a telescope"],
        solutions: ["Context", "Knowledge bases", "Machine learning", "Human annotation"]
      },
      {
        challenge: "Language Variability",
        description: "Differences within and between languages",
        aspects: ["Dialects", "Slang", "Evolving vocabulary", "Code switching"],
        impact: ["Model generalization", "Coverage", "Accuracy", "User experience"],
        approaches: ["Data diversity", "Transfer learning", "Domain adaptation", "Continual learning"]
      },
      {
        challenge: "Data Scarcity",
        description: "Limited labeled data for training",
        causes: ["Cost", "Privacy", "Expertise", "Time"],
        solutions: ["Transfer learning", "Data augmentation", "Semi-supervised learning", "Active learning"],
        techniques: ["Synthetic data", "Crowdsourcing", "Weak supervision", "Self-supervision"]
      },
      {
        challenge: "Computational Resources",
        description: "High computational requirements",
        factors: ["Model size", "Data volume", "Training time", "Inference speed"],
        optimization: ["Model compression", "Distillation", "Quantization", "Efficient architectures"],
        tradeoffs: ["Accuracy vs speed", "Memory vs performance", "Cost vs quality", "Complexity vs maintainability"]
      },
      {
        challenge: "Evaluation",
        description: "Measuring NLP system performance",
        issues: ["Subjectivity", "Multiple metrics", "Domain specificity", "Human factors"],
        approaches: ["Automatic metrics", "Human evaluation", "A/B testing", "Task-specific evaluation"],
        considerations: ["Bias", "Fairness", "Interpretability", "Explainability"]
      },
      {
        challenge: "Ethics and Bias",
        description: "Ethical considerations in NLP",
        concerns: ["Fairness", "Privacy", "Transparency", "Accountability"],
        issues: ["Stereotypes", "Discrimination", "Misinformation", "Manipulation"],
        solutions: ["Bias detection", "Fairness constraints", "Explainable AI", "Ethical guidelines"]
      }
    ]
    
    challenges.each do |challenge|
      puts "#{challenge[:challenge]}:"
      puts "  Description: #{challenge[:description]}"
      puts "  Types: #{challenge[:types].join(', ')}" if challenge[:types]
      puts "  Examples: #{challenge[:examples].join(', ')}" if challenge[:examples]
      puts "  Solutions: #{challenge[:solutions].join(', ')}" if challenge[:solutions]
      puts "  Aspects: #{challenge[:aspects].join(', ')}" if challenge[:aspects]
      puts "  Impact: #{challenge[:impact].join(', ')}" if challenge[:impact]
      puts "  Approaches: #{challenge[:approaches].join(', ')}" if challenge[:approaches]
      puts "  Causes: #{challenge[:causes].join(', ')}" if challenge[:causes]
      puts "  Factors: #{challenge[:factors].join(', ')}" if challenge[:factors]
      puts "  Optimization: #{challenge[:optimization].join(', ')}" if challenge[:optimization]
      puts "  Tradeoffs: #{challenge[:tradeoffs].join(', ')}" if challenge[:tradeoffs]
      puts "  Issues: #{challenge[:issues].join(', ')}" if challenge[:issues]
      puts "  Concerns: #{challenge[:concerns].join(', ')}" if challenge[:concerns]
      puts
    end
  end
  
  # Run NLP fundamentals
  explain_nlp_concepts
  nlp_pipeline
  nlp_applications
  nlp_challenges
end
```

### 2. Text Preprocessing

Text preprocessing and cleaning:

```ruby
class TextPreprocessor
  def initialize(options = {})
    @language = options[:language] || :english
    @lowercase = options[:lowercase] != false
    @remove_punctuation = options[:remove_punctuation] || false
    @remove_numbers = options[:remove_numbers] || false
    @remove_stopwords = options[:remove_stopwords] || false
    @stemming = options[:stemming] || false
    @lemmatization = options[:lemmatization] || false
    @min_word_length = options[:min_word_length] || 1
    @max_word_length = options[:max_word_length] || 50
    
    # Load stopwords
    @stopwords = load_stopwords(@language)
    
    # Initialize stemmer/lemmatizer
    @stemmer = @stemming ? PorterStemmer.new : nil
    @lemmatizer = @lemmatization ? Lemmatizer.new : nil
  end
  
  def preprocess(text)
    # 1. Clean text
    cleaned = clean_text(text)
    
    # 2. Tokenize
    tokens = tokenize(cleaned)
    
    # 3. Normalize tokens
    tokens = normalize_tokens(tokens)
    
    # 4. Filter tokens
    tokens = filter_tokens(tokens)
    
    # 5. Apply stemming/lemmatization
    tokens = apply_stemming_lemmatization(tokens)
    
    tokens
  end
  
  def preprocess_batch(texts)
    texts.map { |text| preprocess(text) }
  end
  
  def clean_text(text)
    # Remove extra whitespace
    text = text.gsub(/\s+/, ' ').strip
    
    # Handle Unicode normalization
    text = normalize_unicode(text)
    
    # Remove special characters (optional)
    text = remove_special_characters(text) if @remove_punctuation || @remove_numbers
    
    text
  end
  
  def tokenize(text)
    # Simple word tokenization
    # In production, use proper tokenizers like spaCy, NLTK, or MeCab
    tokens = text.split(/\s+/)
    
    # Handle contractions and special cases
    tokens = handle_contractions(tokens)
    
    tokens
  end
  
  def normalize_tokens(tokens)
    tokens.map do |token|
      # Lowercase
      token = token.downcase if @lowercase
      
      # Remove leading/trailing punctuation
      token = token.gsub(/^[^\w]+|[^\w]+$/, '')
      
      token
    end
  end
  
  def filter_tokens(tokens)
    tokens.select do |token|
      # Remove empty tokens
      next false if token.empty?
      
      # Remove stopwords
      next false if @remove_stopwords && @stopwords.include?(token)
      
      # Remove numbers
      next false if @remove_numbers && token.match?(/^\d+$/)
      
      # Length filters
      next false if token.length < @min_word_length
      next false if token.length > @max_word_length
      
      true
    end
  end
  
  def apply_stemming_lemmatization(tokens)
    tokens.map do |token|
      if @lemmatization && @lemmatizer
        @lemmatizer.lemmatize(token)
      elsif @stemming && @stemmer
        @stemmer.stem(token)
      else
        token
      end
    end
  end
  
  def self.demonstrate_preprocessing
    puts "Text Preprocessing Demonstration:"
    puts "=" * 50
    
    # Sample texts
    texts = [
      "The quick brown fox jumps over the lazy dog!",
      "Natural Language Processing is amazing!!!",
      "I can't believe it's already 2023...",
      "Machine learning and AI are transforming the world.",
      "Text preprocessing is an important step in NLP."
    ]
    
    # Create preprocessor with different configurations
    configs = [
      { name: "Basic", options: {} },
      { name: "Lowercase + Stopwords", options: { lowercase: true, remove_stopwords: true } },
      { name: "Full preprocessing", options: { 
        lowercase: true, 
        remove_punctuation: true, 
        remove_stopwords: true, 
        stemming: true 
      }}
    ]
    
    configs.each do |config|
      puts "\n#{config[:name]} Configuration:"
      
      preprocessor = TextPreprocessor.new(config[:options])
      
      texts.each_with_index do |text, i|
        original = text
        processed = preprocessor.preprocess(text)
        
        puts "  Text #{i + 1}:"
        puts "    Original: #{original}"
        puts "    Processed: #{processed.join(' ')}"
        puts
      end
    end
    
    # Demonstrate batch processing
    puts "\nBatch Processing:"
    
    preprocessor = TextPreprocessor.new(lowercase: true, remove_stopwords: true)
    batch_results = preprocessor.preprocess_batch(texts)
    
    batch_results.each_with_index do |tokens, i|
      puts "  Text #{i + 1}: #{tokens.join(' ')}"
    end
    
    puts "\nText Preprocessing Features:"
    puts "- Text cleaning and normalization"
    puts "- Tokenization"
    puts "- Stop word removal"
    puts "- Stemming and lemmatization"
    puts "- Configurable preprocessing pipeline"
    puts "- Batch processing support"
    puts "- Language-specific handling"
  end
  
  private
  
  def normalize_unicode(text)
    # Unicode normalization (NFKC)
    text.encode('UTF-8', 'UTF-8', universal_newline: true)
  end
  
  def remove_special_characters(text)
    # Remove punctuation and numbers based on configuration
    text = text.gsub(/[^\w\s]/, '') if @remove_punctuation
    text = text.gsub(/\d+/, '') if @remove_numbers
    text
  end
  
  def handle_contractions(tokens)
    # Simple contraction handling
    contractions = {
      "can't" => "cannot",
      "won't" => "will not",
      "i'm" => "i am",
      "you're" => "you are",
      "we're" => "we are",
      "they're" => "they are",
      "it's" => "it is",
      "that's" => "that is",
      "there's" => "there is",
      "here's" => "here is",
      "what's" => "what is",
      "who's" => "who is",
      "where's" => "where is",
      "when's" => "when is",
      "why's" => "why is",
      "how's" => "how is",
      "i've" => "i have",
      "you've" => "you have",
      "we've" => "we have",
      "they've" => "they have",
      "i'd" => "i would",
      "you'd" => "you would",
      "we'd" => "we would",
      "they'd" => "they would",
      "i'll" => "i will",
      "you'll" => "you will",
      "we'll" => "we will",
      "they'll" => "they will"
    }
    
    tokens.map do |token|
      contractions[token.downcase] || token
    end
  end
  
  def load_stopwords(language)
    # Simple stopwords list
    case language
    when :english
      %w[
        a about above after again against all am an and any are aren't as at be because been before being below between both but by can't cannot could couldn't did didn't do does doesn't doing don't down during each few for from further had hadn't has hasn't have haven't having he he'd he'll he's her here here's hers herself him himself his how how's i i'd i'll i'm i've if in into is isn't it it's its itself just let's me more most mustn't my myself no nor not now of off on once only or other ought our ours ourselves out over own same shan't she she'd she'll she's should shouldn't so some such than that that's that'll the their theirs them themselves then there there's these they they'd they'll they're they've this those through to too under until up very was wasn't we we'd we'll we're we've were weren't what what's when when's where where's which while who who's whom why why's with won't would wouldn't you you'd you'll you're you've your yours yourself yourselves
      ]
    else
      []
    end
  end
end

class PorterStemmer
  def initialize
    @vowels = %w[a e i o u]
  end
  
  def stem(word)
    return word if word.length <= 2
    
    word = word.downcase
    
    # Step 1a: Plural, past tense, participle
    word = step1a(word)
    
    # Step 1b: Remove ed, ing, at, bl, iz
    word = step1b(word)
    
    # Step 1c: Convert y to i if preceded by consonant
    word = step1c(word)
    
    # Step 2: Remove double endings
    word = step2(word)
    
    # Step 3: Remove ionic endings
    word = step3(word)
    
    # Step 4: Remove final e
    word = step4(word)
    
    # Step 5a: Remove double consonant
    word = step5a(word)
    
    # Step 5b: Remove final s
    word = step5b(word)
    
    word
  end
  
  private
  
  def step1a(word)
    # Plural forms
    if word.end_with?('sses')
      word[0...-2]
    elsif word.end_with?('ies')
      word[0...-3] + 'y'
    elsif word.end_with?('s') && !word.end_with?('ss')
      word[0...-1]
    else
      word
    end
  end
  
  def step1b(word)
    # Past tense and participles
    if word.end_with?('eed')
      stem = word[0...-3]
      if measure(stem) > 0
        stem + 'ee'
      else
        word
      end
    elsif word.end_with?('ed') || word.end_with?('ing')
      stem = word[0...-2]
      if contains_vowel(stem)
        if word.end_with?('ing') && stem.end_with?('at')
          stem + 'at' + 'e'
        elsif word.end_with?('ing') && stem.end_with?('bl')
          stem + 'bl' + 'e'
        elsif word.end_with?('ing') && stem.end_with?('iz')
          stem + 'iz' + 'e'
        elsif measure(stem) == 1 && double_consonant(stem)
          stem[0...-1]
        elsif measure(stem) == 1 && ends_cvc(stem)
          stem + 'e'
        else
          stem
        end
      else
        word
      end
    else
      word
    end
  end
  
  def step1c(word)
    if word.end_with?('y') && !contains_vowel(word[0...-1])
      word[0...-1] + 'i'
    else
      word
    end
  end
  
  def step2(word)
    suffixes = %w[ationalization ization ation ator alism iveness fulness ousness aliti iviti biliti]
    
    suffixes.each do |suffix|
      if word.end_with?(suffix)
        stem = word[0...-suffix.length]
        if measure(stem) > 0
          case suffix
          when 'ationalization'
            stem + 'ate'
          when 'ization'
            stem + 'ize'
          when 'ation'
            stem + 'ate'
          when 'ator'
            stem + 'ate'
          when 'alism'
            stem + 'al'
          when 'iveness'
            stem + 'ive'
          when 'fulness'
            stem + 'ful'
          when 'ousness'
            stem + 'ous'
          when 'aliti'
            stem + 'al'
          when 'iviti'
            stem + 'ive'
          when 'biliti'
            stem + 'ble'
          end
        else
          word
        end
      end
    end
    
    word
  end
  
  def step3(word)
    suffixes = %w[icate ative alize icate iciti ical ful ness]
    
    suffixes.each do |suffix|
      if word.end_with?(suffix)
        stem = word[0...-suffix.length]
        if measure(stem) > 0
          case suffix
          when 'icate'
            stem + 'ic'
          when 'ative'
            stem + ''
          when 'alize'
            stem + 'al'
          when 'icate'
            stem + 'ic'
          when 'iciti'
            stem + 'ic'
          when 'ical'
            stem + 'ic'
          when 'ful'
            stem
          when 'ness'
            stem
          end
        else
          word
        end
      end
    end
    
    word
  end
  
  def step4(word)
    suffixes = %w[al ance ence er ic able ible ant ement ment ent ness ive ize]
    
    suffixes.each do |suffix|
      if word.end_with?(suffix)
        stem = word[0...-suffix.length]
        if measure(stem) > 1
          stem
        else
          word
        end
      end
    end
    
    word
  end
  
  def step5a(word)
    if word.end_with?('e')
      stem = word[0...-1]
      if measure(stem) > 1 || (measure(stem) == 1 && !ends_cvc(stem))
        stem
      else
        word
      end
    else
      word
    end
  end
  
  def step5b(word)
    if measure(word) > 1 && double_consonant(word) && word.end_with?('l')
      word[0...-1]
    else
      word
    end
  end
  
  def measure(stem)
    # Count the number of consonant sequences
    count = 0
    i = 0
    
    while i < stem.length
      # Skip consonants
      while i < stem.length && !vowel?(stem[i])
        i += 1
      end
      
      # Count vowel sequence
      if i < stem.length && vowel?(stem[i])
        i += 1
        count += 1
      end
      
      # Skip vowels
      while i < stem.length && vowel?(stem[i])
        i += 1
      end
      
      # Count consonant sequence
      if i < stem.length && !vowel?(stem[i])
        i += 1
        count += 1
      end
    end
    
    count
  end
  
  def contains_vowel(word)
    word.each_char { |c| return true if vowel?(c) }
    false
  end
  
  def vowel?(char)
    @vowels.include?(char)
  end
  
  def double_consonant(word)
    return false if word.length < 2
    word[-1] == word[-2] && !vowel?(word[-1])
  end
  
  def ends_cvc(word)
    return false if word.length < 3
    !vowel?(word[-1]) && vowel?(word[-2]) && !vowel?(word[-3]) && 
    !%w[w x y].include?(word[-1])
  end
end

class Lemmatizer
  def initialize
    @lemmas = load_lemmas
  end
  
  def lemmatize(word)
    word = word.downcase
    @lemmas[word] || word
  end
  
  private
  
  def load_lemmas
    # Simple lemmatization dictionary
    {
      'cars' => 'car',
      'dogs' => 'dog',
      'cats' => 'cat',
      'running' => 'run',
      'ran' => 'run',
      'runs' => 'run',
      'eaten' => 'eat',
      'ate' => 'eat',
      'eating' => 'eat',
      'was' => 'be',
      'were' => 'be',
      'is' => 'be',
      'are' => 'be',
      'been' => 'be',
      'being' => 'be',
      'had' => 'have',
      'has' => 'have',
      'having' => 'have',
      'went' => 'go',
      'gone' => 'go',
      'going' => 'go',
      'said' => 'say',
      'says' => 'say',
      'saying' => 'say',
      'came' => 'come',
      'comes' => 'come',
      'coming' => 'come',
      'saw' => 'see',
      'sees' => 'see',
      'seeing' => 'see',
      'made' => 'make',
      'makes' => 'make',
      'making' => 'make',
      'took' => 'take',
      'takes' => 'take',
      'taking' => 'take',
      'knew' => 'know',
      'knows' => 'know',
      'knowing' => 'know',
      'thought' => 'think',
      'thinks' => 'think',
      'thinking' => 'think',
      'found' => 'find',
      'finds' => 'find',
      'finding' => 'find',
      'got' => 'get',
      'gets' => 'get',
      'getting' => 'get',
      'gave' => 'give',
      'gives' => 'give',
      'giving' => 'give',
      'told' => 'tell',
      'tells' => 'tell',
      'telling' => 'tell',
      'bought' => 'buy',
      'buys' => 'buy',
      'buying' => 'buy',
      'brought' => 'bring',
      'brings' => 'bring',
      'bringing' => 'bring',
      'chose' => 'choose',
      'chooses' => 'choose',
      'choosing' => 'choose',
      'drew' => 'draw',
      'draws' => 'draw',
      'drawing' => 'draw',
      'drove' => 'drive',
      'drives' => 'drive',
      'driving' => 'drive',
      'ate' => 'eat',
      'eats' => 'eat',
      'eating' => 'eat',
      'fell' => 'fall',
      'falls' => 'fall',
      'falling' => 'fall',
      'felt' => 'feel',
      'feels' => 'feel',
      'feeling' => 'feel',
      'flew' => 'fly',
      'flies' => 'fly',
      'flying' => 'fly',
      'forgot' => 'forget',
      'forgets' => 'forget',
      'forgetting' => 'forget',
      'forgave' => 'forgive',
      'forgives' => 'forgive',
      'forgiving' => 'forgive',
      'froze' => 'freeze',
      'freezes' => 'freeze',
      'freezing' => 'freeze',
      'got' => 'get',
      'gets' => 'get',
      'getting' => 'get',
      'gave' => 'give',
      'gives' => 'give',
      'giving' => 'give',
      'went' => 'go',
      'goes' => 'go',
      'going' => 'go',
      'grew' => 'grow',
      'grows' => 'grow',
      'growing' => 'grow',
      'hung' => 'hang',
      'hangs' => 'hang',
      'hanging' => 'hang',
      'heard' => 'hear',
      'hears' => 'hear',
      'hearing' => 'hear',
      'hid' => 'hide',
      'hides' => 'hide',
      'hiding' => 'hide',
      'held' => 'hold',
      'holds' => 'hold',
      'holding' => 'hold',
      'hurt' => 'hurt',
      'hurts' => 'hurt',
      'hurting' => 'hurt',
      'kept' => 'keep',
      'keeps' => 'keep',
      'keeping' => 'keep',
      'knew' => 'know',
      'knows' => 'know',
      'knowing' => 'know',
      'laid' => 'lay',
      'lays' => 'lay',
      'laying' => 'lay',
      'led' => 'lead',
      'leads' => 'lead',
      'leading' => 'lead',
      'left' => 'leave',
      'leaves' => 'leave',
      'leaving' => 'leave',
      'lent' => 'lend',
      'lends' => 'lend',
      'lending' => 'lend',
      'let' => 'let',
      'lets' => 'let',
      'letting' => 'let',
      'lay' => 'lie',
      'lies' => 'lie',
      'lying' => 'lie',
      'lit' => 'light',
      'lights' => 'light',
      'lighting' => 'light',
      'lost' => 'lose',
      'loses' => 'lose',
      'losing' => 'lose',
      'made' => 'make',
      'makes' => 'make',
      'making' => 'make',
      'meant' => 'mean',
      'means' => 'mean',
      'meaning' => 'mean',
      'met' => 'meet',
      'meets' => 'meet',
      'meeting' => 'meet',
      'paid' => 'pay',
      'pays' => 'pay',
      'paying' => 'pay',
      'put' => 'put',
      'puts' => 'put',
      'putting' => 'put',
      'ran' => 'run',
      'runs' => 'run',
      'running' => 'run',
      'said' => 'say',
      'says' => 'say',
      'saying' => 'say',
      'saw' => 'see',
      'sees' => 'see',
      'seeing' => 'see',
      'sought' => 'seek',
      'seeks' => 'seek',
      'seeking' => 'seek',
      'sold' => 'sell',
      'sells' => 'sell',
      'selling' => 'sell',
      'sent' => 'send',
      'sends' => 'send',
      'sending' => 'send',
      'set' => 'set',
      'sets' => 'set',
      'setting' => 'set',
      'sewed' => 'sew',
      'sews' => 'sew',
      'sewing' => 'sew',
      'shook' => 'shake',
      'shakes' => 'shake',
      'shaking' => 'shake',
      'shone' => 'shine',
      'shines' => 'shine',
      'shining' => 'shine',
      'shot' => 'shoot',
      'shoots' => 'shoot',
      'shooting' => 'shoot',
      'showed' => 'show',
      'shows' => 'show',
      'showing' => 'show',
      'shrank' => 'shrink',
      'shrinks' => 'shrink',
      'shrinking' => 'shrink',
      'shut' => 'shut',
      'shuts' => 'shut',
      'shutting' => 'shut',
      'sang' => 'sing',
      'sings' => 'sing',
      'singing' => 'sing',
      'sank' => 'sink',
      'sinks' => 'sink',
      'sinking' => 'sink',
      'sat' => 'sit',
      'sits' => 'sit',
      'sitting' => 'sit',
      'slept' => 'sleep',
      'sleeps' => 'sleep',
      'sleeping' => 'sleep',
      'slid' => 'slide',
      'slides' => 'slide',
      'sliding' => 'slide',
      'slung' => 'sling',
      'slings' => 'sling',
      'slinging' => 'sling',
      'smelled' => 'smell',
      'smells' => 'smell',
      'smelling' => 'smell',
      'snuck' => 'sneak',
      'sneaks' => 'sneak',
      'sneaking' => 'sneak',
      'sold' => 'sell',
      'sells' => 'sell',
      'selling' => 'sell',
      'solved' => 'solve',
      'solves' => 'solve',
      'solving' => 'solve',
      'spoke' => 'speak',
      'speaks' => 'speak',
      'speaking' => 'speak',
      'spent' => 'spend',
      'spends' => 'spend',
      'spending' => 'spend',
      'spilled' => 'spill',
      'spills' => 'spill',
      'spilling' => 'spill',
      'spun' => 'spin',
      'spins' => 'spin',
      'spinning' => 'spin',
      'split' => 'split',
      'splits' => 'split',
      'splitting' => 'split',
      'spread' => 'spread',
      'spreads' => 'spread',
      'spreading' => 'spread',
      'sprang' => 'spring',
      'springs' => 'spring',
      'springing' => 'spring',
      'stood' => 'stand',
      'stands' => 'stand',
      'standing' => 'stand',
      'stole' => 'steal',
      'steals' => 'steal',
      'stealing' => 'steal',
      'stuck' => 'stick',
      'sticks' => 'stick',
      'sticking' => 'stick',
      'stung' => 'sting',
      'stings' => 'sting',
      'stinging' => 'sting',
      'stank' => 'stink',
      'stinks' => 'stink',
      'stinking' => 'stink',
      'strode' => 'stride',
      'strides' => 'stride',
      'striding' => 'stride',
      'struck' => 'strike',
      'strikes' => 'strike',
      'striking' => 'strike',
      'swore' => 'swear',
      'swears' => 'swear',
      'swearing' => 'swear',
      'swept' => 'sweep',
      'sweeps' => 'sweep',
      'sweeping' => 'sweep',
      'swelled' => 'swell',
      'swells' => 'swell',
      'swelling' => 'swell',
      'swam' => 'swim',
      'swims' => 'swim',
      'swimming' => 'swim',
      'swung' => 'swing',
      'swings' => 'swing',
      'swinging' => 'swing',
      'took' => 'take',
      'takes' => 'take',
      'taking' => 'take',
      'taught' => 'teach',
      'teaches' => 'teach',
      'teaching' => 'teach',
      'tore' => 'tear',
      'tears' => 'tear',
      'tearing' => 'tear',
      'told' => 'tell',
      'tells' => 'tell',
      'telling' => 'tell',
      'thought' => 'think',
      'thinks' => 'think',
      'thinking' => 'think',
      'threw' => 'throw',
      'throws' => 'throw',
      'throwing' => 'throw',
      'thrust' => 'thrust',
      'thrusts' => 'thrust',
      'thrusting' => 'thrust',
      'trod' => 'tread',
      'treads' => 'tread',
      'treading' => 'tread',
      'woke' => 'wake',
      'wakes' => 'wake',
      'waking' => 'wake',
      'wore' => 'wear',
      'wears' => 'wear',
      'wearing' => 'wear',
      'wove' => 'weave',
      'weaves' => 'weave',
      'weaving' => 'weave',
      'wed' => 'wed',
      'weds' => 'wed',
      'wedding' => 'wed',
      'wept' => 'weep',
      'weeps' => 'weep',
      'weeping' => 'weep',
      'won' => 'win',
      'wins' => 'win',
      'winning' => 'win',
      'withdrew' => 'withdraw',
      'withdraws' => 'withdraw',
      'withdrawing' => 'withdraw',
      'withheld' => 'withhold',
      'withholds' => 'withhold',
      'withholding' => 'withhold',
      'withstood' => 'withstand',
      'withstands' => 'withstand',
      'withstanding' => 'withstand',
      'wrote' => 'write',
      'writes' => 'write',
      'writing' => 'write',
      'wrung' => 'wring',
      'wrings' => 'wring',
      'wringing' => 'wring'
    }
  end
end
```

## 📊 Text Representation

### 3. Text Feature Extraction

Converting text to numerical features:

```ruby
class TextRepresentation
  def self.demonstrate_text_representation
    puts "Text Representation Demonstration:"
    puts "=" * 50
    
    # Sample documents
    documents = [
      "The quick brown fox jumps over the lazy dog",
      "Natural language processing is a fascinating field",
      "Machine learning algorithms help us understand text",
      "Text preprocessing is an important step in NLP",
      "Vector representations enable computers to process language"
    ]
    
    # 1. Bag of Words
    demonstrate_bag_of_words(documents)
    
    # 2. TF-IDF
    demonstrate_tfidf(documents)
    
    # 3. N-grams
    demonstrate_ngrams(documents)
    
    # 4. Word Embeddings
    demonstrate_word_embeddings(documents)
    
    # 5. Document Embeddings
    demonstrate_document_embeddings(documents)
    
    # 6. Feature Comparison
    demonstrate_feature_comparison(documents)
  end
  
  def self.demonstrate_bag_of_words(documents)
    puts "\n1. Bag of Words:"
    puts "=" * 30
    
    bow = BagOfWords.new
    vectors = bow.fit_transform(documents)
    
    puts "Vocabulary size: #{bow.vocabulary.length}"
    puts "Sample vocabulary: #{bow.vocabulary.first(10)}"
    
    puts "\nDocument vectors (first 2 documents, first 10 features):"
    vectors.first(2).each_with_index do |vector, i|
      puts "  Document #{i + 1}: #{vector.first(10)}"
    end
    
    puts "\nBag of Words Features:"
    puts "- Simple word frequency counting"
    puts "- Fixed-size vector representation"
    puts "- Easy to implement and understand"
    puts "- Loses word order information"
    puts "- High dimensional for large vocabularies"
  end
  
  def self.demonstrate_tfidf(documents)
    puts "\n2. TF-IDF:"
    puts "=" * 30
    
    tfidf = TFIDFVectorizer.new
    vectors = tfidf.fit_transform(documents)
    
    puts "Vocabulary size: #{tfidf.vocabulary.length}"
    puts "IDF values (first 10 words):"
    tfidf.vocabulary.first(10).each do |word, idx|
      puts "  #{word}: #{tfidf.idf_values[idx].round(4)}"
    end
    
    puts "\nTF-IDF vectors (first document, first 10 features):"
    vectors.first.first(10).each_with_index do |value, i|
      word = tfidf.vocabulary.keys[i]
      puts "  #{word}: #{value.round(4)}"
    end
    
    puts "\nTF-IDF Features:"
    puts "- Term frequency weighting"
    puts "- Inverse document frequency"
    puts "- Reduces impact of common words"
    puts "- Better than bag of words"
    puts "- Still loses word order"
  end
  
  def self.demonstrate_ngrams(documents)
    puts "\n3. N-grams:"
    puts "=" * 30
    
    # Unigrams
    unigrams = NGramVectorizer.new(n: 1)
    unigram_vectors = unigrams.fit_transform(documents)
    
    # Bigrams
    bigrams = NGramVectorizer.new(n: 2)
    bigram_vectors = bigrams.fit_transform(documents)
    
    # Trigrams
    trigrams = NGramVectorizer.new(n: 3)
    trigram_vectors = trigrams.fit_transform(documents)
    
    puts "Unigram vocabulary size: #{unigrams.vocabulary.length}"
    puts "Bigram vocabulary size: #{bigrams.vocabulary.length}"
    puts "Trigram vocabulary size: #{trigrams.vocabulary.length}"
    
    puts "\nSample bigrams (first 10):"
    bigrams.vocabulary.first(10).each { |ngram| puts "  #{ngram}" }
    
    puts "\nN-gram Features:"
    puts "- Captures local word order"
    puts "- Preserves some context"
    puts "- Can handle phrases"
    puts "- Increases vocabulary size"
    puts "- Higher n-grams are sparse"
  end
  
  def self.demonstrate_word_embeddings(documents)
    puts "\n4. Word Embeddings:"
    puts "=" * 30
    
    # Create word embeddings
    embeddings = WordEmbeddings.new(embedding_size: 50)
    embeddings.train(documents, epochs: 100)
    
    puts "Embedding size: #{embeddings.embedding_size}"
    puts "Vocabulary size: #{embeddings.vocabulary.length}"
    
    # Show similar words
    sample_words = ['language', 'processing', 'machine', 'learning']
    
    puts "\nWord similarities:"
    sample_words.each do |word|
      if embeddings.vocabulary.include?(word)
        similar = embeddings.most_similar(word, top_k: 3)
        puts "  #{word}: #{similar.map { |w, s| "#{w}(#{s.round(3)})" }.join(', ')}"
      end
    end
    
    # Show word vectors
    puts "\nWord vectors (sample):"
    sample_words.each do |word|
      if embeddings.vocabulary.include?(word)
        vector = embeddings.get_vector(word)
        puts "  #{word}: [#{vector.first(5).map { |v| v.round(3) }.join(', ')}, ...]"
      end
    end
    
    puts "\nWord Embeddings Features:"
    puts "- Dense vector representations"
    puts "- Captures semantic relationships"
    puts "- Reduces dimensionality"
    puts "- Enables similarity calculations"
    puts "- Can be pretrained"
  end
  
  def self.demonstrate_document_embeddings(documents)
    puts "\n5. Document Embeddings:"
    puts "=" * 30
    
    # Create document embeddings
    doc_embeddings = DocumentEmbeddings.new(embedding_size: 100)
    doc_embeddings.train(documents, epochs: 50)
    
    puts "Embedding size: #{doc_embeddings.embedding_size}"
    puts "Number of documents: #{documents.length}"
    
    # Show document vectors
    puts "\nDocument vectors (sample):"
    doc_embeddings.vectors.each_with_index do |vector, i|
      puts "  Document #{i + 1}: [#{vector.first(5).map { |v| v.round(3) }.join(', ')}, ...]"
    end
    
    # Show document similarities
    puts "\nDocument similarities:"
    doc_embeddings.vectors.each_with_index do |vector1, i|
      doc_embeddings.vectors.each_with_index do |vector2, j|
        next if i >= j
        similarity = cosine_similarity(vector1, vector2)
        puts "  Doc #{i + 1} - Doc #{j + 1}: #{similarity.round(3)}"
      end
    end
    
    puts "\nDocument Embeddings Features:"
    puts "- Fixed-size document vectors"
    puts "- Captures document meaning"
    puts "- Enables similarity search"
    puts "- Useful for classification"
    puts "- Can be aggregated from word embeddings"
  end
  
  def self.demonstrate_feature_comparison(documents)
    puts "\n6. Feature Comparison:"
    puts "=" * 30
    
    # Compare different representations
    bow = BagOfWords.new
    tfidf = TFIDFVectorizer.new
    ngrams = NGramVectorizer.new(n: 2)
    
    bow_vectors = bow.fit_transform(documents)
    tfidf_vectors = tfidf.fit_transform(documents)
    ngram_vectors = ngrams.fit_transform(documents)
    
    puts "Representation comparison:"
    puts "  Bag of Words:"
    puts "    Vocabulary size: #{bow.vocabulary.length}"
    puts "    Vector sparsity: #{calculate_sparsity(bow_vectors).round(3)}"
    puts "    Average vector norm: #{average_norm(bow_vectors).round(3)}"
    
    puts "  TF-IDF:"
    puts "    Vocabulary size: #{tfidf.vocabulary.length}"
    puts "    Vector sparsity: #{calculate_sparsity(tfidf_vectors).round(3)}"
    puts "    Average vector norm: #{average_norm(tfidf_vectors).round(3)}"
    
    puts "  Bigrams:"
    puts "    Vocabulary size: #{ngrams.vocabulary.length}"
    puts "    Vector sparsity: #{calculate_sparsity(ngram_vectors).round(3)}"
    puts "    Average vector norm: #{average_norm(ngram_vectors).round(3)}"
    
    puts "\nFeature Comparison Insights:"
    puts "- Bag of words: Simple but sparse"
    puts "- TF-IDF: Better weighting, less sparse"
    puts "- N-grams: More context, much sparser"
    puts "- Word embeddings: Dense, semantic"
    puts "- Document embeddings: Fixed size, meaningful"
  end
  
  private
  
  def self.cosine_similarity(vector1, vector2)
    dot_product = vector1.zip(vector2).map { |a, b| a * b }.sum
    norm1 = Math.sqrt(vector1.map { |x| x * x }.sum)
    norm2 = Math.sqrt(vector2.map { |x| x * x }.sum)
    
    dot_product / (norm1 * norm2)
  end
  
  def self.calculate_sparsity(vectors)
    total_elements = vectors.length * vectors.first.length
    non_zero_elements = vectors.sum { |v| v.count { |x| x != 0 } }
    
    1.0 - (non_zero_elements.to_f / total_elements)
  end
  
  def self.average_norm(vectors)
    norms = vectors.map { |v| Math.sqrt(v.map { |x| x * x }.sum) }
    norms.sum / norms.length
  end
end

class BagOfWords
  def initialize
    @vocabulary = {}
    @vocabulary_index = {}
    @document_frequency = {}
  end
  
  attr_reader :vocabulary
  
  def fit_transform(documents)
    # Build vocabulary
    documents.each_with_index do |doc, doc_id|
      tokens = tokenize(doc)
      unique_tokens = tokens.uniq
      
      unique_tokens.each do |token|
        @vocabulary[token] ||= @vocabulary.length
        @vocabulary_index[@vocabulary[token]] = token
        @document_frequency[token] ||= 0
        @document_frequency[token] += 1
      end
    end
    
    # Transform documents
    vectors = documents.map do |doc|
      vector = Array.new(@vocabulary.length, 0)
      tokens = tokenize(doc)
      
      tokens.each do |token|
        if @vocabulary.key?(token)
          index = @vocabulary[token]
          vector[index] += 1
        end
      end
      
      vector
    end
    
    vectors
  end
  
  private
  
  def tokenize(text)
    # Simple tokenization
    text.downcase.gsub(/[^\w\s]/, '').split(/\s+/)
  end
end

class TFIDFVectorizer
  def initialize
    @vocabulary = {}
    @vocabulary_index = {}
    @document_frequency = {}
    @idf_values = {}
    @num_documents = 0
  end
  
  attr_reader :vocabulary, :idf_values
  
  def fit_transform(documents)
    @num_documents = documents.length
    
    # Build vocabulary and document frequencies
    documents.each_with_index do |doc, doc_id|
      tokens = tokenize(doc)
      unique_tokens = tokens.uniq
      
      unique_tokens.each do |token|
        @vocabulary[token] ||= @vocabulary.length
        @vocabulary_index[@vocabulary[token]] = token
        @document_frequency[token] ||= 0
        @document_frequency[token] += 1
      end
    end
    
    # Calculate IDF values
    @vocabulary.each do |token, index|
      df = @document_frequency[token]
      @idf_values[index] = Math.log(@num_documents.to_f / df)
    end
    
    # Transform documents
    vectors = documents.map do |doc|
      vector = Array.new(@vocabulary.length, 0)
      tokens = tokenize(doc)
      token_counts = Hash.new(0)
      
      tokens.each { |token| token_counts[token] += 1 }
      
      token_counts.each do |token, count|
        if @vocabulary.key?(token)
          index = @vocabulary[token]
          tf = count.to_f / tokens.length
          idf = @idf_values[index]
          vector[index] = tf * idf
        end
      end
      
      vector
    end
    
    vectors
  end
  
  private
  
  def tokenize(text)
    text.downcase.gsub(/[^\w\s]/, '').split(/\s+/)
  end
end

class NGramVectorizer
  def initialize(n: 2)
    @n = n
    @vocabulary = {}
    @vocabulary_index = {}
    @document_frequency = {}
    @num_documents = 0
  end
  
  attr_reader :vocabulary
  
  def fit_transform(documents)
    @num_documents = documents.length
    
    # Build vocabulary
    documents.each_with_index do |doc, doc_id|
      ngrams = generate_ngrams(doc, @n)
      unique_ngrams = ngrams.uniq
      
      unique_ngrams.each do |ngram|
        @vocabulary[ngram] ||= @vocabulary.length
        @vocabulary_index[@vocabulary[ngram]] = ngram
        @document_frequency[ngram] ||= 0
        @document_frequency[ngram] += 1
      end
    end
    
    # Transform documents
    vectors = documents.map do |doc|
      vector = Array.new(@vocabulary.length, 0)
      ngrams = generate_ngrams(doc, @n)
      
      ngrams.each do |ngram|
        if @vocabulary.key?(ngram)
          index = @vocabulary[ngram]
          vector[index] += 1
        end
      end
      
      vector
    end
    
    vectors
  end
  
  private
  
  def generate_ngrams(text, n)
    tokens = tokenize(text)
    return [] if tokens.length < n
    
    (0..tokens.length - n).map do |i|
      tokens[i...i + n].join(' ')
    end
  end
  
  def tokenize(text)
    text.downcase.gsub(/[^\w\s]/, '').split(/\s+/)
  end
end

class WordEmbeddings
  def initialize(embedding_size: 100, learning_rate: 0.01, window_size: 5)
    @embedding_size = embedding_size
    @learning_rate = learning_rate
    @window_size = window_size
    @vocabulary = {}
    @embeddings = {}
    @training_data = []
  end
  
  attr_reader :vocabulary, :embedding_size
  
  def train(documents, epochs: 100)
    # Build vocabulary
    all_tokens = documents.map { |doc| tokenize(doc) }.flatten
    @vocabulary = all_tokens.uniq.each_with_index.to_h
    @vocabulary.each { |word, index| @embeddings[word] = random_vector }
    
    # Generate training data (skip-gram)
    documents.each do |doc|
      tokens = tokenize(doc)
      tokens.each_with_index do |target, i|
        # Get context words
        start = [i - @window_size, 0].max
        ending = [i + @window_size, tokens.length - 1].min
        context = tokens[start...i] + tokens[i + 1..ending]
        
        context.each do |context_word|
          @training_data << [target, context_word]
        end
      end
    end
    
    # Train embeddings
    epochs.times do |epoch|
      @training_data.shuffle.each do |target, context|
        update_embeddings(target, context)
      end
      
      puts "Epoch #{epoch + 1}: Training completed" if (epoch + 1) % 20 == 0
    end
  end
  
  def get_vector(word)
    @embeddings[word] || zero_vector
  end
  
  def most_similar(word, top_k: 5)
    return [] unless @embeddings.key?(word)
    
    target_vector = @embeddings[word]
    similarities = {}
    
    @vocabulary.each do |other_word, _|
      next if other_word == word
      other_vector = @embeddings[other_word]
      
      similarity = cosine_similarity(target_vector, other_vector)
      similarities[other_word] = similarity
    end
    
    similarities.sort_by { |_, sim| -sim }.first(top_k)
  end
  
  private
  
  def tokenize(text)
    text.downcase.gsub(/[^\w\s]/, '').split(/\s+/)
  end
  
  def random_vector
    Array.new(@embedding_size) { rand(-0.1..0.1) }
  end
  
  def zero_vector
    Array.new(@embedding_size, 0)
  end
  
  def update_embeddings(target, context)
    target_vector = @embeddings[target]
    context_vector = @embeddings[context]
    
    # Simple update rule (simplified skip-gram)
    # In practice, use negative sampling and more sophisticated methods
    similarity = cosine_similarity(target_vector, context_vector)
    
    # Update target vector
    target_vector.each_with_index do |_, i|
      target_vector[i] += @learning_rate * context_vector[i] * (1 - similarity)
    end
    
    # Update context vector
    context_vector.each_with_index do |_, i|
      context_vector[i] += @learning_rate * target_vector[i] * (1 - similarity)
    end
  end
  
  def cosine_similarity(vector1, vector2)
    dot_product = vector1.zip(vector2).map { |a, b| a * b }.sum
    norm1 = Math.sqrt(vector1.map { |x| x * x }.sum)
    norm2 = Math.sqrt(vector2.map { |x| x * x }.sum)
    
    return 0 if norm1 == 0 || norm2 == 0
    
    dot_product / (norm1 * norm2)
  end
end

class DocumentEmbeddings
  def initialize(embedding_size: 100, learning_rate: 0.01)
    @embedding_size = embedding_size
    @learning_rate = learning_rate
    @vectors = []
    @word_embeddings = WordEmbeddings.new(embedding_size: 50)
  end
  
  attr_reader :vectors, :embedding_size
  
  def train(documents, epochs: 50)
    # Train word embeddings first
    @word_embeddings.train(documents, epochs: epochs)
    
    # Create document embeddings by averaging word embeddings
    @vectors = documents.map do |doc|
      tokens = tokenize(doc)
      vectors = tokens.map { |token| @word_embeddings.get_vector(token) }
      
      if vectors.empty?
        zero_vector
      else
        # Average word vectors
        vectors.reduce(:+) { |sum, vec| sum.zip(vec).map { |a, b| a + b } }
                   .map { |x| x / vectors.length }
      end
    end
    
    # Fine-tune document embeddings
    epochs.times do |epoch|
      # Simple document similarity training
      @vectors.each_with_index do |vector1, i|
        @vectors.each_with_index do |vector2, j|
          next if i >= j
          
          # Update based on similarity
          similarity = cosine_similarity(vector1, vector2)
          update_vectors(vector1, vector2, similarity)
        end
      end
    end
  end
  
  def most_similar(document_index, top_k: 3)
    return [] if document_index >= @vectors.length
    
    target_vector = @vectors[document_index]
    similarities = []
    
    @vectors.each_with_index do |vector, i|
      next if i == document_index
      
      similarity = cosine_similarity(target_vector, vector)
      similarities << [i, similarity]
    end
    
    similarities.sort_by { |_, sim| -sim }.first(top_k)
  end
  
  private
  
  def tokenize(text)
    text.downcase.gsub(/[^\w\s]/, '').split(/\s+/)
  end
  
  def zero_vector
    Array.new(@embedding_size, 0)
  end
  
  def update_vectors(vector1, vector2, similarity)
    # Simple update rule
    adjustment = @learning_rate * (1 - similarity)
    
    vector1.each_with_index do |_, i|
      vector1[i] += adjustment * vector2[i]
      vector2[i] += adjustment * vector1[i]
    end
  end
  
  def cosine_similarity(vector1, vector2)
    dot_product = vector1.zip(vector2).map { |a, b| a * b }.sum
    norm1 = Math.sqrt(vector1.map { |x| x * x }.sum)
    norm2 = Math.sqrt(vector2.map { |x| x * x }.sum)
    
    return 0 if norm1 == 0 || norm2 == 0
    
    dot_product / (norm1 * norm2)
  end
end
```

## 🤖 Text Classification

### 4. Text Classification Algorithms

Building text classification systems:

```ruby
class TextClassification
  def self.demonstrate_text_classification
    puts "Text Classification Demonstration:"
    puts "=" * 50
    
    # Sample dataset
    dataset = create_sample_dataset
    
    # 1. Naive Bayes Classifier
    demonstrate_naive_bayes(dataset)
    
    # 2. SVM Classifier
    demonstrate_svm(dataset)
    
    # 3. Neural Network Classifier
    demonstrate_neural_network(dataset)
    
    # 4. Ensemble Methods
    demonstrate_ensemble(dataset)
    
    # 5. Evaluation Metrics
    demonstrate_evaluation(dataset)
    
    # 6. Cross-Validation
    demonstrate_cross_validation(dataset)
  end
  
  def self.demonstrate_naive_bayes(dataset)
    puts "\n1. Naive Bayes Classifier:"
    puts "=" * 30
    
    # Split dataset
    train_data, test_data = split_dataset(dataset, 0.8)
    
    # Create classifier
    classifier = NaiveBayesClassifier.new
    classifier.train(train_data)
    
    # Test classifier
    predictions = test_data.map { |doc, _| classifier.predict(doc) }
    actual = test_data.map { |_, label| label }
    
    # Calculate metrics
    accuracy = calculate_accuracy(predictions, actual)
    precision = calculate_precision(predictions, actual)
    recall = calculate_recall(predictions, actual)
    f1 = calculate_f1_score(precision, recall)
    
    puts "Training samples: #{train_data.length}"
    puts "Test samples: #{test_data.length}"
    puts "Accuracy: #{accuracy.round(4)}"
    puts "Precision: #{precision.round(4)}"
    puts "Recall: #{recall.round(4)}"
    puts "F1-Score: #{f1.round(4)}"
    
    # Show some predictions
    puts "\nSample predictions:"
    test_data.first(3).each_with_index do |(text, actual), i|
      predicted = predictions[i]
      puts "  Text #{i + 1}: Predicted: #{predicted}, Actual: #{actual}"
    end
    
    puts "\nNaive Bayes Features:"
    puts "- Probabilistic classification"
    "- Independence assumption"
    "- Fast training and prediction"
    "- Works well with text"
    "- Handles multiple classes"
  end
  
  def self.demonstrate_svm(dataset)
    puts "\n2. SVM Classifier:"
    puts "=" * 30
    
    # Split dataset
    train_data, test_data = split_dataset(dataset, 0.8)
    
    # Create classifier
    classifier = SVMClassifier.new(kernel: :linear, C: 1.0)
    classifier.train(train_data)
    
    # Test classifier
    predictions = test_data.map { |doc, _| classifier.predict(doc) }
    actual = test_data.map { |_, label| label }
    
    # Calculate metrics
    accuracy = calculate_accuracy(predictions, actual)
    precision = calculate_precision(predictions, actual)
    recall = calculate_recall(predictions, actual)
    f1 = calculate_f1_score(precision, recall)
    
    puts "Training samples: #{train_data.length}"
    puts "Test samples: #{test_data.length}"
    puts "Accuracy: #{accuracy.round(4)}"
    puts "Precision: #{precision.round(4)}"
    puts "Recall: #{recall.round(4)}"
    puts "F1-Score: #{f1.round(4)}"
    
    # Show some predictions
    puts "\nSample predictions:"
    test_data.first(3).each_with_index do |(text, actual), i|
      predicted = predictions[i]
      puts "  Text #{i + 1}: Predicted: #{predicted}, Actual: #{actual}"
    end
    
    puts "\nSVM Features:"
    puts "- Maximum margin classifier"
    "- Kernel trick for non-linearity"
    "- Effective in high dimensions"
    "- Good generalization"
    "- Multiple kernel options"
  end
  
  def self.demonstrate_neural_network(dataset)
    puts "\n3. Neural Network Classifier:"
    puts "=" * 30
    
    # Split dataset
    train_data, test_data = split_dataset(dataset, 0.8)
    
    # Create classifier
    classifier = NeuralNetworkClassifier.new(
      input_size: 1000, # Fixed vocabulary size
      hidden_sizes: [128, 64],
      output_size: 3,    # 3 classes
      learning_rate: 0.001
    )
    classifier.train(train_data, epochs: 50)
    
    # Test classifier
    predictions = test_data.map { |doc, _| classifier.predict(doc) }
    actual = test_data.map { |_, label| label }
    
    # Calculate metrics
    accuracy = calculate_accuracy(predictions, actual)
    precision = calculate_precision(predictions, actual)
    recall = calculate_recall(predictions, actual)
    f1 = calculate_f1_score(precision, recall)
    
    puts "Training samples: #{train_data.length}"
    puts "Test samples: #{test_data.length}"
    puts "Accuracy: #{accuracy.round(4)}"
    puts "Precision: #{precision.round(4)}"
    puts "Recall: #{recall.round(4)}"
    puts "F1-Score: #{f1.round(4)}"
    
    # Show some predictions
    puts "\nSample predictions:"
    test_data.first(3).each_with_index do |(text, actual), i|
      predicted = predictions[i]
      puts "  Text #{i + 1}: Predicted: #{predicted}, Actual: #{actual}"
    end
    
    puts "\nNeural Network Features:"
    puts "- Non-linear classification"
    "- Feature learning"
    "- Flexible architecture"
    "- End-to-end learning"
    "- Can handle complex patterns"
  end
  
  def self.demonstrate_ensemble(dataset)
    puts "\n4. Ensemble Methods:"
    puts "=" * 30
    
    # Split dataset
    train_data, test_data = split_dataset(dataset, 0.8)
    
    # Create ensemble classifier
    classifier = EnsembleClassifier.new(
      classifiers: [
        NaiveBayesClassifier.new,
        SVMClassifier.new(kernel: :linear),
        NeuralNetworkClassifier.new(input_size: 1000, hidden_sizes: [64, 32], output_size: 3)
      ],
      voting: :soft
    )
    classifier.train(train_data)
    
    # Test classifier
    predictions = test_data.map { |doc, _| classifier.predict(doc) }
    actual = test_data.map { |_, label| label }
    
    # Calculate metrics
    accuracy = calculate_accuracy(predictions, actual)
    precision = calculate_precision(predictions, actual)
    recall = calculate_recall(predictions, actual)
    f1 = calculate_f1_score(precision, recall)
    
    puts "Training samples: #{train_data.length}"
    puts "Test samples: #{test_data.length}"
    puts "Accuracy: #{accuracy.round(4)}"
    puts "Precision: #{precision.round(4)}"
    puts "Recall: #{recall.round(4)}"
    puts "F1-Score: #{f1.round(4)}"
    
    # Show some predictions
    puts "\nSample predictions:"
    test_data.first(3).each_with_index do |(text, actual), i|
      predicted = predictions[i]
      puts "  Text #{i + 1}: Predicted: #{predicted}, Actual: #{actual}"
    end
    
    puts "\nEnsemble Features:"
    puts "- Combines multiple classifiers"
    "- Improved performance"
    "- Reduces overfitting"
    "- Robust to errors"
    "- Multiple voting strategies"
  end
  
  def self.demonstrate_evaluation(dataset)
    puts "\n5. Evaluation Metrics:"
    puts "=" * 30
    
    # Create and train classifier
    train_data, test_data = split_dataset(dataset, 0.8)
    classifier = NaiveBayesClassifier.new
    classifier.train(train_data)
    
    # Get predictions
    predictions = test_data.map { |doc, _| classifier.predict(doc) }
    actual = test_data.map { |_, label| label }
    
    # Calculate detailed metrics
    metrics = calculate_detailed_metrics(predictions, actual)
    
    puts "Overall Metrics:"
    puts "  Accuracy: #{metrics[:accuracy].round(4)}"
    puts "  Precision: #{metrics[:precision].round(4)}"
    puts "  Recall: #{metrics[:recall].round(4)}"
    puts "  F1-Score: #{metrics[:f1_score].round(4)}"
    
    puts "\nPer-Class Metrics:"
    metrics[:per_class].each do |class_name, class_metrics|
      puts "  #{class_name}:"
      puts "    Precision: #{class_metrics[:precision].round(4)}"
      puts "    Recall: #{class_metrics[:recall].round(4)}"
      puts "    F1-Score: #{class_metrics[:f1_score].round(4)}"
    end
    
    puts "\nConfusion Matrix:"
    print_confusion_matrix(metrics[:confusion_matrix])
    
    puts "\nEvaluation Features:"
    puts "- Multiple evaluation metrics"
    "- Per-class performance"
    "- Confusion matrix"
    "- Detailed analysis"
    "- Error analysis"
  end
  
  def self.demonstrate_cross_validation(dataset)
    puts "\n6. Cross-Validation:"
    puts "=" * 30
    
    # Create classifier
    classifier = NaiveBayesClassifier.new
    
    # Perform k-fold cross-validation
    cv_results = cross_validate(classifier, dataset, k: 5)
    
    puts "Cross-Validation Results (k=5):"
    puts "  Mean Accuracy: #{cv_results[:mean_accuracy].round(4)}"
    puts "  Std Accuracy: #{cv_results[:std_accuracy].round(4)}"
    puts "  Mean Precision: #{cv_results[:mean_precision].round(4)}"
    puts "  Std Precision: #{cv_results[:std_precision].round(4)}"
    puts "  Mean Recall: #{cv_results[:mean_recall].round(4)}"
    puts "  Std Recall: #{cv_results[:std_recall].round(4)}"
    puts "  Mean F1-Score: #{cv_results[:mean_f1].round(4)}"
    puts "  Std F1-Score: #{cv_results[:std_f1].round(4)}"
    
    puts "\nFold Results:"
    cv_results[:fold_results].each_with_index do |fold, i|
      puts "  Fold #{i + 1}: Accuracy: #{fold[:accuracy].round(4)}, F1: #{fold[:f1].round(4)}"
    end
    
    puts "\nCross-Validation Features:"
    puts "- Reliable performance estimation"
    "- Reduces overfitting bias"
    "- Hyperparameter tuning"
    "- Model comparison"
    "- Statistical significance"
  end
  
  private
  
  def self.create_sample_dataset
    [
      ["The weather is beautiful today", "positive"],
      ["I love this new phone", "positive"],
      ["This movie is amazing", "positive"],
      ["Great service at the restaurant", "positive"],
      ["I'm so happy about this", "positive"],
      ["The food was terrible", "negative"],
      ["I hate waiting in line", "negative"],
      ["This is the worst experience", "negative"],
      ["Poor customer service", "negative"],
      ["I'm disappointed with the product", "negative"],
      ["The meeting is scheduled for tomorrow", "neutral"],
      ["Please review the documentation", "neutral"],
      ["The package will arrive next week", "neutral"],
      ["Follow the instructions carefully", "neutral"],
      ["The system requires maintenance", "neutral"]
    ]
  end
  
  def self.split_dataset(dataset, train_ratio)
    shuffled = dataset.shuffle
    split_index = (shuffled.length * train_ratio).to_i
    
    [shuffled[0...split_index], shuffled[split_index..-1]]
  end
  
  def self.calculate_accuracy(predictions, actual)
    correct = predictions.zip(actual).count { |p, a| p == a }
    correct.to_f / predictions.length
  end
  
  def self.calculate_precision(predictions, actual)
    classes = actual.uniq
    precisions = classes.map do |class_name|
      true_positives = predictions.zip(actual).count { |p, a| p == class_name && a == class_name }
      predicted_positives = predictions.count { |p| p == class_name }
      
      predicted_positives > 0 ? true_positives.to_f / predicted_positives : 0
    end
    
    precisions.sum / precisions.length
  end
  
  def self.calculate_recall(predictions, actual)
    classes = actual.uniq
    recalls = classes.map do |class_name|
      true_positives = predictions.zip(actual).count { |p, a| p == class_name && a == class_name }
      actual_positives = actual.count { |a| a == class_name }
      
      actual_positives > 0 ? true_positives.to_f / actual_positives : 0
    end
    
    recalls.sum / recalls.length
  end
  
  def self.calculate_f1_score(precision, recall)
    return 0 if precision == 0 && recall == 0
    2 * (precision * recall) / (precision + recall)
  end
  
  def self.calculate_detailed_metrics(predictions, actual)
    classes = actual.uniq
    confusion_matrix = Hash.new { |h, k| h[k] = Hash.new(0) }
    
    # Build confusion matrix
    predictions.zip(actual).each do |pred, act|
      confusion_matrix[act][pred] += 1
    end
    
    # Calculate per-class metrics
    per_class = {}
    classes.each do |class_name|
      tp = confusion_matrix[class_name][class_name]
      fp = (confusion_matrix[class_name].values.sum - tp)
      fn = (classes.sum { |c| confusion_matrix[c][class_name] } - tp)
      tn = (predictions.length - tp - fp - fn)
      
      precision = (tp + fp) > 0 ? tp.to_f / (tp + fp) : 0
      recall = (tp + fn) > 0 ? tp.to_f / (tp + fn) : 0
      f1 = calculate_f1_score(precision, recall)
      
      per_class[class_name] = {
        precision: precision,
        recall: recall,
        f1_score: f1
      }
    end
    
    # Calculate overall metrics
    overall_precision = calculate_precision(predictions, actual)
    overall_recall = calculate_recall(predictions, actual)
    overall_f1 = calculate_f1_score(overall_precision, overall_recall)
    overall_accuracy = calculate_accuracy(predictions, actual)
    
    {
      accuracy: overall_accuracy,
      precision: overall_precision,
      recall: overall_recall,
      f1_score: overall_f1,
      per_class: per_class,
      confusion_matrix: confusion_matrix
    }
  end
  
  def self.print_confusion_matrix(confusion_matrix)
    classes = confusion_matrix.keys.sort
    
    # Header
    print "    "
    classes.each { |c| print "#{c.to_s.ljust(10)}" }
    puts
    
    # Rows
    classes.each do |actual_class|
      print "#{actual_class.to_s.ljust(4)}"
      classes.each do |predicted_class|
        count = confusion_matrix[actual_class][predicted_class]
        print "#{count.to_s.ljust(10)}"
      end
      puts
    end
  end
  
  def self.cross_validate(classifier, dataset, k: 5)
    fold_size = dataset.length / k
    fold_results = []
    
    k.times do |fold|
      # Split data
      test_start = fold * fold_size
      test_end = (fold + 1) * fold_size
      
      test_data = dataset[test_start...test_end]
      train_data = dataset[0...test_start] + dataset[test_end..-1]
      
      # Train and test
      classifier_copy = classifier.clone
      classifier_copy.train(train_data)
      
      predictions = test_data.map { |doc, _| classifier_copy.predict(doc) }
      actual = test_data.map { |_, label| label }
      
      # Calculate metrics
      accuracy = calculate_accuracy(predictions, actual)
      precision = calculate_precision(predictions, actual)
      recall = calculate_recall(predictions, actual)
      f1 = calculate_f1_score(precision, recall)
      
      fold_results << {
        accuracy: accuracy,
        precision: precision,
        recall: recall,
        f1: f1
      }
    end
    
    # Calculate mean and standard deviation
    accuracies = fold_results.map { |r| r[:accuracy] }
    precisions = fold_results.map { |r| r[:precision] }
    recalls = fold_results.map { |r| r[:recall] }
    f1s = fold_results.map { |r| r[:f1] }
    
    {
      mean_accuracy: accuracies.sum / accuracies.length,
      std_accuracy: calculate_std(accuracies),
      mean_precision: precisions.sum / precisions.length,
      std_precision: calculate_std(precisions),
      mean_recall: recalls.sum / recalls.length,
      std_recall: calculate_std(recalls),
      mean_f1: f1s.sum / f1s.length,
      std_f1: calculate_std(f1s),
      fold_results: fold_results
    }
  end
  
  def self.calculate_std(values)
    mean = values.sum / values.length
    variance = values.map { |v| (v - mean) ** 2 }.sum / values.length
    Math.sqrt(variance)
  end
end

class NaiveBayesClassifier
  def initialize
    @class_priors = Hash.new(0)
    @word_likelihoods = Hash.new { |h, k| h[k] = Hash.new(0) }
    @word_totals = Hash.new(0)
    @class_totals = Hash.new(0)
    @vocabulary = Set.new
  end
  
  def train(training_data)
    # Count classes and words
    training_data.each do |text, class_name|
      @class_priors[class_name] += 1
      words = tokenize(text)
      
      words.each do |word|
        @word_likelihoods[class_name][word] += 1
        @word_totals[class_name] += 1
        @vocabulary << word
      end
    end
    
    # Calculate priors
    total_samples = training_data.length
    @class_priors.each do |class_name, count|
      @class_priors[class_name] = count.to_f / total_samples
    end
    
    # Calculate likelihoods with Laplace smoothing
    @vocabulary.each do |word|
      @word_likelihoods.each do |class_name, _|
        word_count = @word_likelihoods[class_name][word]
        total_words = @word_totals[class_name]
        
        # Laplace smoothing
        @word_likelihoods[class_name][word] = (word_count + 1).to_f / (total_words + @vocabulary.length)
      end
    end
  end
  
  def predict(text)
    words = tokenize(text)
    class_scores = {}
    
    @class_priors.each do |class_name, prior|
      log_prob = Math.log(prior)
      
      words.each do |word|
        if @vocabulary.include?(word)
          log_prob += Math.log(@word_likelihoods[class_name][word])
        else
          # Word not in vocabulary - use small probability
          log_prob += Math.log(1.0 / (@word_totals[class_name] + @vocabulary.length))
        end
      end
      
      class_scores[class_name] = log_prob
    end
    
    # Return class with highest score
    class_scores.max_by { |_, score| score }.first
  end
  
  private
  
  def tokenize(text)
    text.downcase.gsub(/[^\w\s]/, '').split(/\s+/)
  end
end

class SVMClassifier
  def initialize(kernel: :linear, C: 1.0)
    @kernel = kernel
    @C = C
    @weights = nil
    @bias = 0
    @support_vectors = []
    @support_labels = []
    @support_alphas = []
  end
  
  def train(training_data)
    # Simplified SVM implementation
    # In practice, use libraries like libsvm or scikit-learn
    
    # Convert text to features (simple bag of words)
    features = training_data.map { |text, _| text_to_features(text) }
    labels = training_data.map { |_, label| label_to_number(label) }
    
    # Train linear SVM (simplified)
    @weights, @bias = train_linear_svm(features, labels)
  end
  
  def predict(text)
    features = text_to_features(text)
    score = features.zip(@weights).map { |f, w| f * w }.sum + @bias
    
    score > 0 ? 'positive' : 'negative'
  end
  
  private
  
  def text_to_features(text)
    # Simple bag of words with fixed vocabulary
    vocabulary = %w[the weather is beautiful today love this new phone movie amazing great service restaurant hate waiting line worst experience poor customer please review documentation package will arrive next week instructions carefully system requires maintenance]
    words = tokenize(text)
    
    vocabulary.map { |word| words.include?(word) ? 1 : 0 }
  end
  
  def tokenize(text)
    text.downcase.gsub(/[^\w\s]/, '').split(/\s+/)
  end
  
  def label_to_number(label)
    case label
    when 'positive'
      1
    when 'negative'
      -1
    else
      0
    end
  end
  
  def train_linear_svm(features, labels)
    # Simplified linear SVM training
    # In practice, use optimization algorithms like SGD or SMO
    
    num_features = features.first.length
    weights = Array.new(num_features, 0)
    bias = 0
    learning_rate = 0.01
    epochs = 100
    
    epochs.times do
      features.each_with_index do |feature_vector, i|
        label = labels[i]
        
        # Calculate margin
        margin = label * (feature_vector.zip(weights).map { |f, w| f * w }.sum + bias)
        
        # Update weights if margin < 1
        if margin < 1
          feature_vector.each_with_index do |feature, j|
            weights[j] += learning_rate * label * feature
          end
          bias += learning_rate * label
        end
      end
    end
    
    [weights, bias]
  end
end

class NeuralNetworkClassifier
  def initialize(input_size:, hidden_sizes:, output_size:, learning_rate: 0.001)
    @input_size = input_size
    @hidden_sizes = hidden_sizes
    @output_size = output_size
    @learning_rate = learning_rate
    
    # Build network
    @network = build_network
  end
  
  def train(training_data, epochs: 100)
    # Convert text to features
    features = training_data.map { |text, _| text_to_features(text) }
    labels = training_data.map { |_, label| label_to_vector(label) }
    
    # Train network
    epochs.times do |epoch|
      total_loss = 0
      
      features.each_with_index do |feature_vector, i|
        target = labels[i]
        
        # Forward pass
        output = @network.forward(feature_vector)
        
        # Calculate loss
        loss = calculate_loss(output, target)
        total_loss += loss
        
        # Backward pass
        @network.backward(feature_vector, target, @learning_rate)
      end
      
      puts "Epoch #{epoch + 1}: Loss = #{total_loss / features.length}" if (epoch + 1) % 10 == 0
    end
  end
  
  def predict(text)
    features = text_to_features(text)
    output = @network.forward(features)
    
    # Convert output to class
    class_index = output.index(output.max)
    
    case class_index
    when 0
      'positive'
    when 1
      'negative'
    else
      'neutral'
    end
  end
  
  private
  
  def build_network
    layers = []
    
    # Input to first hidden layer
    layers << DenseLayer.new(@input_size, @hidden_sizes[0], activation: :relu)
    
    # Hidden layers
    @hidden_sizes.each_cons do |input_size, output_size|
      layers << DenseLayer.new(input_size, output_size, activation: :relu)
    end
    
    # Output layer
    layers << DenseLayer.new(@hidden_sizes.last, @output_size, activation: :softmax)
    
    NeuralNetwork.new(layers)
  end
  
  def text_to_features(text)
    # Simple bag of words with fixed vocabulary
    vocabulary = %w[the weather is beautiful today love this new phone movie amazing great service restaurant hate waiting line worst experience poor customer please review documentation package will arrive next week instructions carefully system requires maintenance]
    words = tokenize(text)
    
    vocabulary.map { |word| words.include?(word) ? 1 : 0 }
  end
  
  def tokenize(text)
    text.downcase.gsub(/[^\w\s]/, '').split(/\s+/)
  end
  
  def label_to_vector(label)
    case label
    when 'positive'
      [1, 0, 0]
    when 'negative'
      [0, 1, 0]
    else
      [0, 0, 1]
    end
  end
  
  def calculate_loss(output, target)
    # Cross-entropy loss
    output.zip(target).map { |o, t| -t * Math.log(o + 1e-10) }.sum
  end
end

class EnsembleClassifier
  def initialize(classifiers:, voting: :soft)
    @classifiers = classifiers
    @voting = voting
  end
  
  def train(training_data)
    @classifiers.each { |classifier| classifier.train(training_data) }
  end
  
  def predict(text)
    if @voting == :hard
      # Hard voting - majority vote
      votes = @classifiers.map { |classifier| classifier.predict(text) }
      votes.group_by(&:itself).max_by { |_, v| v.length }.first
    else
      # Soft voting - weighted by confidence
      votes = @classifiers.map { |classifier| classifier.predict(text) }
      votes.group_by(&:itself).max_by { |_, v| v.length }.first
    end
  end
  
  def clone
    # Create deep copy of classifiers
    cloned_classifiers = @classifiers.map { |c| c.clone }
    EnsembleClassifier.new(classifiers: cloned_classifiers, voting: @voting)
  end
end

# Supporting classes for neural network
class NeuralNetwork
  def initialize(layers)
    @layers = layers
  end
  
  def forward(input)
    @layers.each do |layer|
      input = layer.forward(input)
    end
    input
  end
  
  def backward(input, target, learning_rate)
    # Forward pass
    output = forward(input)
    
    # Calculate error
    error = output.zip(target).map { |o, t| o - t }
    
    # Backward pass
    @layers.reverse.each do |layer|
      error = layer.backward(error, learning_rate)
    end
  end
end

class DenseLayer
  def initialize(input_size, output_size, activation: :linear)
    @input_size = input_size
    @output_size = output_size
    @activation = activation
    
    # Initialize weights and bias
    @weights = Array.new(output_size) { Array.new(input_size) { rand(-0.1..0.1) } }
    @bias = Array.new(output_size, 0)
    
    @last_input = nil
    @last_output = nil
  end
  
  def forward(input)
    @last_input = input
    
    # Linear transformation
    output = @weights.map do |weight_row|
      weight_row.zip(input).map { |w, x| w * x }.sum + @bias[weight_row.index(w)]
    end
    
    # Apply activation
    @last_output = apply_activation(output)
    @last_output
  end
  
  def backward(error, learning_rate)
    # Calculate gradient
    activation_grad = activation_derivative(@last_output)
    output_grad = error.map { |e| e * activation_grad }
    
    # Update weights and bias
    @weights.each_with_index do |weight_row, i|
      weight_row.each_with_index do |weight, j|
        @weights[i][j] -= learning_rate * output_grad[i] * @last_input[j]
      end
      @bias[i] -= learning_rate * output_grad[i]
    end
    
    # Calculate input gradient
    input_grad = Array.new(@input_size, 0)
    @weights.each_with_index do |weight_row, i|
      weight_row.each_with_index do |weight, j|
        input_grad[j] += output_grad[i] * weight
      end
    end
    
    input_grad
  end
  
  private
  
  def apply_activation(input)
    case @activation
    when :relu
      input.map { |x| [x, 0].max }
    when :sigmoid
      input.map { |x| 1 / (1 + Math.exp(-x)) }
    when :tanh
      input.map { |x| Math.tanh(x) }
    when :softmax
      exp_x = input.map { |x| Math.exp(x) }
      sum_exp = exp_x.sum
      exp_x.map { |x| x / sum_exp }
    else
      input
    end
  end
  
  def activation_derivative(output)
    case @activation
    when :relu
      output.map { |x| x > 0 ? 1 : 0 }
    when :sigmoid
      output.map { |x| x * (1 - x) }
    when :tanh
      output.map { |x| 1 - x * x }
    when :softmax
      # Simplified softmax derivative
      output.map { |x| x * (1 - x) }
    else
      output.map { 1 }
    end
  end
end
```

## 🎯 Exercises

### Beginner Exercises

1. **Text Preprocessing**: Implement text cleaning and tokenization
2. **Bag of Words**: Create simple text representation
3. **Text Classification**: Build basic classifier
4. **Evaluation Metrics**: Calculate accuracy, precision, recall

### Intermediate Exercises

1. **TF-IDF**: Implement TF-IDF vectorization
2. **Word Embeddings**: Create word embeddings
3. **Neural Networks**: Build neural text classifier
4. **Cross-Validation**: Implement k-fold cross-validation

### Advanced Exercises

1. **Deep Learning**: Implement transformer-based models
2. **Sequence Models**: Build RNN/LSTM for text
3. **Attention Mechanisms**: Implement attention
4. **Production NLP**: Deploy NLP systems

---

## 🎯 Summary

Natural Language Processing in Ruby provides:

- **NLP Fundamentals** - Core concepts and principles
- **Text Preprocessing** - Cleaning and tokenization
- **Text Representation** - Feature extraction methods
- **Text Classification** - Classification algorithms
- **Comprehensive Examples** - Real-world implementations

Master these NLP techniques for text processing applications!
