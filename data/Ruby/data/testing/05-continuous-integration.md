# Continuous Integration in Ruby

## Overview

Continuous Integration (CI) is the practice of frequently integrating code changes into a shared repository and automatically building and testing each integration. In Ruby development, CI ensures code quality, catches bugs early, and maintains consistency across team members' contributions.

## CI/CD Pipeline Configuration

### GitHub Actions for Ruby
```yaml
# .github/workflows/ci.yml
name: Ruby CI

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

env:
  RUBY_VERSION: '3.2'
  BUNDLE_CACHE: true
  RAILS_ENV: test

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        ruby-version: ['3.0', '3.1', '3.2']
        gemfile: ['Gemfile', 'gemfiles/rails_6.gemfile', 'gemfiles/rails_7.gemfile']
        
    services:
      postgres:
        image: postgres:15
        env:
          POSTGRES_PASSWORD: postgres
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
        ports:
          - 5432:5432
      
      redis:
        image: redis:7
        options: >-
          --health-cmd "redis-cli ping"
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
        ports:
          - 6379:6379

    steps:
    - name: Checkout code
      uses: actions/checkout@v4

    - name: Set up Ruby ${{ matrix.ruby-version }}
      uses: ruby/setup-ruby@v1
      with:
        ruby-version: ${{ matrix.ruby-version }}
        bundler-cache: true
        cache-version: 1

    - name: Install system dependencies
      run: |
        sudo apt-get update
        sudo apt-get install -y postgresql-client libpq-dev redis-tools

    - name: Cache gems
      uses: actions/cache@v3
      with:
        path: vendor/bundle
        key: ${{ runner.os }}-gems-${{ matrix.ruby-version }}-${{ hashFiles('**/Gemfile.lock') }}
        restore-keys: |
          ${{ runner.os }}-gems-${{ matrix.ruby-version }}-

    - name: Install dependencies
      run: |
        gem install bundler
        bundle config set --local deployment true
        bundle config set --local without development
        bundle install --jobs 4 --retry 3
      env:
        BUNDLE_GEMFILE: ${{ matrix.gemfile }}

    - name: Set up database
      run: |
        cp config/database.yml.ci config/database.yml
        bundle exec rails db:create
        bundle exec rails db:schema:load
      env:
        DATABASE_URL: postgresql://postgres:postgres@localhost:5432/test
        BUNDLE_GEMFILE: ${{ matrix.gemfile }}

    - name: Run RuboCop
      run: bundle exec rubocop --parallel
      env:
        BUNDLE_GEMFILE: ${{ matrix.gemfile }}

    - name: Run Brakeman
      run: bundle exec brakeman --exit-on-warn
      env:
        BUNDLE_GEMFILE: ${{ matrix.gemfile }}

    - name: Run security checks
      run: |
        bundle exec bundle-audit check --update
        bundle exec bundle-audit
      env:
        BUNDLE_GEMFILE: ${{ matrix.gemfile }}

    - name: Run RSpec tests
      run: |
        bundle exec rspec --format documentation --format RspecJunitFormatter --out rspec-results.xml
      env:
        COVERAGE: true
        BUNDLE_GEMFILE: ${{ matrix.gemfile }}

    - name: Run Cucumber tests
      run: |
        bundle exec cucumber --format pretty --format junit --out cucumber-results.xml
      env:
        BUNDLE_GEMFILE: ${{ matrix.gemfile }}

    - name: Upload coverage reports
      uses: codecov/codecov-action@v3
      with:
        file: ./coverage/coverage.xml
        flags: ${{ matrix.ruby-version }}
        name: codecov-umbrella

    - name: Upload test results
      uses: actions/upload-artifact@v3
      if: always()
      with:
        name: test-results-${{ matrix.ruby-version }}
        path: |
          rspec-results.xml
          cucumber-results.xml
          coverage/

  security-scan:
    runs-on: ubuntu-latest
    steps:
    - name: Checkout code
      uses: actions/checkout@v4

    - name: Set up Ruby
      uses: ruby/setup-ruby@v1
      with:
        ruby-version: '3.2'

    - name: Install dependencies
      run: |
        gem install bundler
        bundle install

    - name: Run security scan
      run: |
        bundle exec brakeman --exit-on-warn
        bundle exec bundle-audit check --update

  performance-tests:
    runs-on: ubuntu-latest
    needs: test
    steps:
    - name: Checkout code
      uses: actions/checkout@v4

    - name: Set up Ruby
      uses: ruby/setup-ruby@v1
      with:
        ruby-version: '3.2'

    - name: Install dependencies
      run: |
        gem install bundler
        bundle install

    - name: Run performance tests
      run: |
        bundle exec rake performance:benchmark
        bundle exec rake performance:load_test

    - name: Upload performance results
      uses: actions/upload-artifact@v3
      with:
        name: performance-results
        path: tmp/performance/
```

### GitLab CI for Ruby
```yaml
# .gitlab-ci.yml
stages:
  - test
  - security
  - performance
  - deploy

variables:
  RUBY_VERSION: "3.2"
  BUNDLE_CACHE: "true"
  POSTGRES_DB: "test"
  POSTGRES_USER: "postgres"
  POSTGRES_PASSWORD: "postgres"
  DATABASE_URL: "postgresql://postgres:postgres@postgres:5432/test"

.cache:
  paths:
    - vendor/bundle/
    - .bundle/
    - node_modules/

before_script:
  - ruby -v
  - gem install bundler
  - bundle config set --local deployment true
  - bundle config set --local without development
  - bundle install --jobs $(nproc) --retry 3

# Test jobs
test:ruby3.0:
  stage: test
  image: ruby:3.0
  services:
    - postgres:15
    - redis:7
  script:
    - cp config/database.yml.ci config/database.yml
    - bundle exec rails db:create db:schema:load
    - bundle exec rubocop --parallel
    - bundle exec rspec --format documentation
    - bundle exec cucumber
  coverage: '/\(\d+\.\d+\%\) covered/'
  artifacts:
    reports:
      junit: rspec-results.xml
    paths:
      - coverage/
    expire_in: 1 week
  only:
    - branches
    - merge_requests

test:ruby3.1:
  extends: test:ruby3.0
  image: ruby:3.1

test:ruby3.2:
  extends: test:ruby3.0
  image: ruby:3.2

# Security scanning
security:
  stage: security
  image: ruby:3.2
  script:
    - bundle exec brakeman --exit-on-warn
    - bundle exec bundle-audit check --update
  artifacts:
    reports:
      sast: gl-sast-report.json
    paths:
      - brakeman-report.html
    expire_in: 1 week
  only:
    - branches
    - merge_requests

# Performance testing
performance:
  stage: performance
  image: ruby:3.2
  script:
    - bundle exec rake performance:benchmark
    - bundle exec rake performance:load_test
  artifacts:
    reports:
      performance: performance-report.json
    paths:
      - tmp/performance/
    expire_in: 1 week
  only:
    - main
    - develop

# Deployment
deploy_staging:
  stage: deploy
  image: ruby:3.2
  script:
    - echo "Deploying to staging..."
    - bundle exec cap staging deploy
  environment:
    name: staging
    url: https://staging.example.com
  only:
    - develop
  when: manual

deploy_production:
  stage: deploy
  image: ruby:3.2
  script:
    - echo "Deploying to production..."
    - bundle exec cap production deploy
  environment:
    name: production
    url: https://example.com
  only:
    - main
  when: manual
```

### CircleCI Configuration
```yaml
# .circleci/config.yml
version: 2.1

orbs:
  ruby: circleci/ruby@2.0.0

jobs:
  build:
    docker:
      - image: cimg/ruby:3.2-node
      - image: cimg/postgres:15
        environment:
          POSTGRES_USER: postgres
          POSTGRES_PASSWORD: postgres
          POSTGRES_DB: test
      - image: cimg/redis:7

    working_directory: ~/app

    steps:
      - checkout

      - ruby/install-deps:
          key: gems-v1-{{ checksum "Gemfile.lock" }}
          cache-version: 1
          bundle-cache-path: vendor/bundle

      - ruby/rspec-test:
          test-command: bundle exec rspec --format documentation --format RspecJunitFormatter --out rspec-results.xml

      - ruby/brakeman-scan

      - ruby/bundle-audit-check

      - store_test_results:
          path: rspec-results.xml

      - store_artifacts:
          path: coverage/
          destination: coverage

  security:
    docker:
      - image: cimg/ruby:3.2

    working_directory: ~/app

    steps:
      - checkout

      - ruby/install-deps

      - ruby/brakeman-scan

      - ruby/bundle-audit-check

      - store_artifacts:
          path: brakeman-report.html

workflows:
  version: 2
  build-and-test:
    jobs:
      - build
      - security:
          requires:
            - build
```

## Test Configuration

### RSpec Configuration for CI
```ruby
# spec/spec_helper.rb
require 'simplecov'
require 'simplecov-json'

SimpleCov.start 'rails' do
  add_filter '/spec/'
  add_filter '/config/'
  add_filter '/db/'
  add_filter '/lib/tasks/'
  
  add_group 'Models', 'app/models'
  add_group 'Controllers', 'app/controllers'
  add_group 'Services', 'app/services'
  add_group 'Workers', 'app/workers'
  add_group 'Lib', 'lib'
  
  minimum_coverage 90
  minimum_coverage_by_file 80
  
  track_files '{app,lib}/**/*.rb'
  
  if ENV['CI']
    use_merges
    merge_timeout 600
    
    # Enable JSON formatter for CI
    formatters SimpleCov::Formatter::HTMLFormatter,
                 SimpleCov::Formatter::JSONFormatter
  else
    formatters SimpleCov::Formatter::HTMLFormatter
  end
end

# Configure RSpec for CI
RSpec.configure do |config|
  config.use_transactional_fixtures = true
  config.infer_spec_type_from_file_location!
  config.filter_rails_from_backtrace!
  
  # CI-specific configuration
  if ENV['CI']
    config.order = :random
    config.seed = ENV['RSPEC_SEED'] || rand(10000)
    
    # Enable parallel testing
    if defined?(ParallelTests)
      require 'parallel_tests'
      
      config.before(:suite) do
        ParallelTests.activate
      end
    end
    
    # Use faster test runner in CI
    config.formatter = :documentation
    config.color = false
  end
  
  # Database cleaner for non-Rails apps
  config.before(:suite) do
    DatabaseCleaner.strategy = :transaction
    DatabaseCleaner.clean_with(:truncation)
  end
  
  config.around(:each) do |example|
    DatabaseCleaner.cleaning do
      example.run
    end
  end
  
  # Mock external services in CI
  config.before(:each) do
    allow(HTTParty).to receive(:get).and_return(double('response', code: 200, body: '{}'))
    allow(Net::HTTP).to receive(:start).and_return(double('response', code: 200, body: '{}'))
  end
end

# Performance testing configuration
if ENV['PERFORMANCE_TESTS']
  require 'benchmark'
  
  RSpec.configure do |config|
    config.around(:each, type: :performance) do |example|
      time = Benchmark.realtime do
        example.run
      end
      
      puts "#{example.description}: #{(time * 1000).round(2)}ms"
      
      # Fail if performance threshold exceeded
      max_time = example.metadata[:max_time] || 1.0
      if time > max_time
        fail "Performance threshold exceeded: #{time}s > #{max_time}s"
      end
    end
  end
end
```

### Cucumber Configuration for CI
```ruby
# features/support/env.rb
require 'simplecov'
SimpleCov.start 'rails' do
  add_filter '/features/'
  add_filter '/spec/'
end

# CI-specific configuration
if ENV['CI']
  require 'cucumber/formatter/junit'
  
  # Use faster formatter in CI
  Cucumber::Rails::Database.javascript_strategy = :truncation
  
  # Configure for headless browser testing
  Capybara.default_driver = :selenium_chrome_headless
  Capybara.javascript_driver = :selenium_chrome_headless
  
  # Increase default wait time for CI
  Capybara.default_max_wait_time = 10
  
  # Configure parallel execution
  if defined?(ParallelTests)
    require 'parallel_tests/cucumber/tasks'
    
    ParallelTests::Cucumber::RuntimeLogger.use!
  end
end

# Database cleaner
Before do
  DatabaseCleaner.start
end

After do
  DatabaseCleaner.clean
end

# Performance testing
Around('@performance') do |scenario, block|
  time = Benchmark.realtime do
    block.call
  end
  
  puts "#{scenario.name}: #{(time * 1000).round(2)}ms"
  
  # Store performance results
  File.open('tmp/performance/cucumber_results.json', 'a') do |f|
    f.puts({
      scenario: scenario.name,
      time: time,
      timestamp: Time.now.iso8601
    }.to_json)
  end
end
```

## Quality Gates and Standards

### Code Quality Configuration
```ruby
# .rubocop.yml
require:
  - rubocop-rails
  - rubocop-rspec
  - rubocop-capybara
  - rubocop-performance
  - rubocop-security

AllCops:
  TargetRubyVersion: 3.2
  NewCops: enable
  Exclude:
    - 'bin/**/*'
    - 'db/**/*'
    - 'config/**/*'
    - 'script/**/*'
    - 'vendor/**/*'
    - 'node_modules/**/*'

# Rails-specific rules
Rails:
  Enabled: true

# RSpec rules
RSpec:
  Enabled: true
  Language:
    Example:
      Descriptions:
        Subject:
          SharedGroups:
            Enabled: false

# Performance rules
Performance:
  Enabled: true
  StringInclude:
    Enabled: false
  SumSize:
    Enabled: false

# Security rules
Security:
  Enabled: true
  Eval:
    Enabled: false  # Allow eval for specific use cases

# Custom rules for CI
Metrics/ClassLength:
  Max: 150

Metrics/MethodLength:
  Max: 25

Metrics/AbcSize:
  Max: 20

Metrics/BlockLength:
  Max: 40

Style/Documentation:
  Enabled: false

Style/ClassAndModuleChildren:
  Enabled: false

Layout/LineLength:
  Max: 120

# CI-specific overrides
if ENV['CI']
  Metrics/ClassLength:
    Max: 200
  
  Metrics/MethodLength:
    Max: 30
  
  Layout/LineLength:
    Max: 150
end
```

### Brakeman Configuration
```ruby
# config/brakeman.yml
# Brakeman configuration for security scanning

# Skip certain checks if necessary
skip_checks:
  - RenderInline  # Allow inline rendering in specific cases
  - MassAssignment  # Allow mass assignment with proper strong parameters

# Check additional files
check_apart_from:
  - app/
  - config/
  - lib/

# Report format
output_files:
  - "brakeman-report.html"
  - "brakeman-report.json"

# Confidence levels
min_confidence_level: 2

# Ignore certain warnings
ignore_warnings:
  - "Dynamic Render Path"
  - "CSRF Protection Bypass"

# Additional configuration
highlight_config: false
run_checks: true
show_progress: true
quiet: false
debug: false
```

### Security Audit Configuration
```ruby
# Gemfile
group :development, :test do
  gem 'brakeman', require: false
  gem 'bundler-audit', require: false
  gem 'simplecov', require: false
  gem 'simplecov-json', require: false
  gem 'rspec_junit_formatter'
  gem 'parallel_tests'
end

# lib/tasks/audit.rake
namespace :audit do
  desc "Run security audits"
  task :security do
    puts "Running Brakeman..."
    system('bundle exec brakeman --exit-on-warn')
    
    puts "Running Bundle Audit..."
    system('bundle exec bundle-audit check --update')
  end
  
  desc "Run code quality checks"
  task :quality do
    puts "Running RuboCop..."
    system('bundle exec rubocop --parallel')
    
    puts "Running Reek..."
    system('bundle exec reek --format html --output reek-report.html')
  end
  
  desc "Run all quality and security checks"
  task :all => [:quality, :security]
end
```

## Performance Monitoring

### Performance Testing in CI
```ruby
# lib/tasks/performance.rake
namespace :performance do
  desc "Run performance benchmarks"
  task :benchmark do
    require 'benchmark'
    require 'memory_profiler'
    
    puts "=== Performance Benchmarks ==="
    
    # Benchmark critical operations
    Benchmark.bm(20) do |x|
      x.report("User creation:") do
        1000.times { User.create(name: "Test User", email: "test@example.com") }
      end
      
      x.report("Database queries:") do
        1000.times { User.first }
      end
      
      x.report("JSON serialization:") do
        users = User.limit(100)
        1000.times { users.to_json }
      end
    end
    
    # Memory profiling
    puts "\n=== Memory Profiling ==="
    
    report = MemoryProfiler.report do
      1000.times do
        user = User.new(name: "Test User", email: "test@example.com")
        user.save
        user.to_json
      end
    end
    
    puts "Total allocated: #{report.total_allocated_memsize} bytes"
    puts "Total retained: #{report.total_retained_memsize} bytes"
    
    # Save results
    FileUtils.mkdir_p('tmp/performance')
    File.write('tmp/performance/benchmark_results.json', {
      timestamp: Time.now.iso8601,
      benchmarks: benchmark_results,
      memory: {
        allocated: report.total_allocated_memsize,
        retained: report.total_retained_memsize
      }
    }.to_json)
  end
  
  desc "Run load tests"
  task :load_test do
    puts "=== Load Testing ==="
    
    # Simple load test simulation
    threads = []
    results = []
    mutex = Mutex.new
    
    10.times do |thread_id|
      threads << Thread.new do
        thread_results = []
        
        100.times do |request_id|
          start_time = Time.now
          
          # Simulate API request
          User.limit(10).to_a
          
          end_time = Time.now
          thread_results << (end_time - start_time) * 1000
        end
        
        mutex.synchronize do
          results.concat(thread_results)
        end
      end
    end
    
    threads.each(&:join)
    
    # Calculate statistics
    avg_response_time = results.sum / results.length
    max_response_time = results.max
    min_response_time = results.min
    
    sorted_results = results.sort
    p95 = sorted_results[sorted_results.length * 0.95]
    p99 = sorted_results[sorted_results.length * 0.99]
    
    puts "Load Test Results:"
    puts "  Total requests: #{results.length}"
    puts "  Average response time: #{avg_response_time.round(2)}ms"
    puts "  Min response time: #{min_response_time.round(2)}ms"
    puts "  Max response time: #{max_response_time.round(2)}ms"
    puts "  95th percentile: #{p95.round(2)}ms"
    puts "  99th percentile: #{p99.round(2)}ms"
    
    # Save results
    File.write('tmp/performance/load_test_results.json', {
      timestamp: Time.now.iso8601,
      total_requests: results.length,
      avg_response_time: avg_response_time,
      max_response_time: max_response_time,
      min_response_time: min_response_time,
      p95: p95,
      p99: p99
    }.to_json)
  end
  
  desc "Run all performance tests"
  task :all => [:benchmark, :load_test]
end
```

### Performance Thresholds
```ruby
# spec/support/performance_examples.rb
RSpec.shared_examples "performance requirements" do |max_time_ms|
  it "completes within #{max_time_ms}ms" do
    start_time = Time.now
    
    # Run the operation
    operation_result = subject
    
    end_time = Time.now
    duration_ms = (end_time - start_time) * 1000
    
    expect(duration_ms).to be <= max_time_ms,
      "Operation took #{duration_ms.round(2)}ms, expected <= #{max_time_ms}ms"
  end
end

# Usage in tests
RSpec.describe "User API", type: :request do
  describe "GET /api/users" do
    subject { get '/api/users' }
    
    it_should_behave_like "performance requirements", 500
  end
  
  describe "POST /api/users" do
    subject do
      post '/api/users', params: { user: { name: "Test User", email: "test@example.com" } }
    end
    
    it_should_behave_like "performance requirements", 200
  end
end
```

## Notification and Reporting

### Slack Notifications
```ruby
# lib/ci/slack_notifier.rb
class SlackNotifier
  def initialize(webhook_url)
    @webhook_url = webhook_url
  end

  def notify_build_start(build_info)
    message = {
      text: "🚀 Build started",
      attachments: [{
        color: 'good',
        fields: [
          { title: 'Branch', value: build_info[:branch], short: true },
          { title: 'Commit', value: build_info[:commit][0..7], short: true },
          { title: 'Author', value: build_info[:author], short: true }
        ],
        actions: [{
          type: 'button',
          text: 'View Build',
          url: build_info[:build_url]
        }]
      }]
    }
    
    send_message(message)
  end

  def notify_build_success(build_info)
    message = {
      text: "✅ Build successful",
      attachments: [{
        color: 'good',
        fields: [
          { title: 'Branch', value: build_info[:branch], short: true },
          { title: 'Duration', value: "#{build_info[:duration]}s", short: true },
          { title: 'Tests', value: "#{build_info[:test_count]} passed", short: true },
          { title: 'Coverage', value: "#{build_info[:coverage]}%", short: true }
        ]
      }]
    }
    
    send_message(message)
  end

  def notify_build_failure(build_info)
    message = {
      text: "❌ Build failed",
      attachments: [{
        color: 'danger',
        fields: [
          { title: 'Branch', value: build_info[:branch], short: true },
          { title: 'Stage', value: build_info[:failed_stage], short: true },
          { title: 'Duration', value: "#{build_info[:duration]}s", short: true },
          { title: 'Error', value: build_info[:error_message], short: false }
        ]
      }]
    }
    
    send_message(message)
  end

  private

  def send_message(message)
    uri = URI(@webhook_url)
    http = Net::HTTP.new(uri.host, uri.port)
    http.use_ssl = uri.scheme == 'https'
    
    request = Net::HTTP::Post.new(uri)
    request['Content-Type'] = 'application/json'
    request.body = message.to_json
    
    http.request(request)
  end
end
```

### Email Notifications
```ruby
# lib/ci/email_notifier.rb
class EmailNotifier
  def initialize(smtp_settings)
    @smtp_settings = smtp_settings
  end

  def notify_build_report(build_info)
    subject = "Build Report: #{build_info[:status].upcase} - #{build_info[:branch]}"
    
    body = <<~EMAIL
      Build Report
      ============
      
      Branch: #{build_info[:branch]}
      Commit: #{build_info[:commit]}
      Author: #{build_info[:author]}
      Status: #{build_info[:status]}
      Duration: #{build_info[:duration]}
      
      Test Results:
      - Total: #{build_info[:test_count]}
      - Passed: #{build_info[:passed_count]}
      - Failed: #{build_info[:failed_count]}
      
      Code Coverage: #{build_info[:coverage]}%
      
      #{"View details: #{build_info[:build_url]}" if build_info[:build_url]}
    EMAIL

    send_email(build_info[:recipients], subject, body)
  end

  private

  def send_email(recipients, subject, body)
    require 'net/smtp'
    
    Net::SMTP.start(@smtp_settings[:address], @smtp_settings[:port]) do |smtp|
      recipients.each do |recipient|
        smtp.send_message(
          "From: #{@smtp_settings[:from]}",
          "To: #{recipient}",
          "Subject: #{subject}",
          body
        )
      end
    end
  end
end
```

## Best Practices

1. **Fast Feedback**: Keep CI pipelines fast and provide quick feedback
2. **Parallel Testing**: Run tests in parallel to reduce execution time
3. **Caching**: Cache dependencies and test data to speed up builds
4. **Quality Gates**: Set minimum standards for code quality and test coverage
5. **Monitoring**: Monitor CI performance and identify bottlenecks
6. **Security**: Include security scanning in CI pipelines
7. **Documentation**: Document CI/CD processes and configurations

## Conclusion

Continuous Integration is essential for maintaining code quality, catching bugs early, and ensuring team productivity in Ruby development. By implementing comprehensive CI/CD pipelines with proper testing, quality gates, and monitoring, teams can deliver high-quality Ruby applications with confidence and efficiency.

## Further Reading

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [GitLab CI/CD Documentation](https://docs.gitlab.com/ee/ci/)
- [CircleCI Documentation](https://circleci.com/docs/)
- [RSpec Best Practices](https://rspec.rubystyleguide.com/)
- [Rails Testing Best Practices](https://github.com/rails/rails/blob/main/guides/testing.md)
