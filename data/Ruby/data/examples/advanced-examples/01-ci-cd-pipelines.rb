# CI/CD Pipelines in Ruby
# This file demonstrates comprehensive CI/CD pipeline implementations for Ruby applications,
# including automated testing, deployment, and monitoring strategies.

module DevOpsExamples
  module CICDPipelines
    # 1. GitHub Actions CI/CD Pipeline
    # Complete CI/CD pipeline using GitHub Actions
    
    class GitHubActionsPipeline
      def self.generate_workflow_file
        workflow = <<~YAML
          name: Ruby CI/CD Pipeline
          
          on:
            push:
              branches: [ main, develop ]
            pull_request:
              branches: [ main ]
            release:
              types: [ published ]
          
          env:
            RUBY_VERSION: '3.2'
            RAILS_ENV: test
            BUNDLE_WITHOUT: development production
          
          jobs:
            # Code Quality and Security
            security:
              runs-on: ubuntu-latest
              steps:
                - name: Checkout code
                  uses: actions/checkout@v4
                  with:
                    fetch-depth: 0
              
                - name: Set up Ruby
                  uses: ruby/setup-ruby@v1
                  with:
                    ruby-version: \${{ env.RUBY_VERSION }}
                    bundler-cache: true
              
                - name: Install dependencies
                  run: bundle install --jobs 4 --retry 3
              
                - name: Run Brakeman security scan
                  run: bundle exec brakeman --exit-on-warn --format json --output-report brakeman-report.json
                  continue-on-error: true
              
                - name: Upload Brakeman report
                  uses: actions/upload-artifact@v4
                  with:
                    name: brakeman-report
                    path: brakeman-report.json
              
                - name: Run bundle audit
                  run: bundle audit --update --format json --output audit-report.json
                  continue-on-error: true
              
                - name: Upload audit report
                  uses: actions/upload-artifact@v4
                  with:
                    name: audit-report
                    path: audit-report.json
              
                - name: Run RuboCop
                  run: bundle exec rubocop --format json --out rubocop-report.json
                  continue-on-error: true
              
                - name: Upload RuboCop report
                  uses: actions/upload-artifact@v4
                  with:
                    name: rubocop-report
                    path: rubocop-report.json
            
            # Test Suite
            test:
              runs-on: ubuntu-latest
              strategy:
                matrix:
                  ruby-version: ['3.0', '3.1', '3.2']
                  rails-version: ['6.1', '7.0']
                  exclude:
                    - ruby-version: '3.0'
                      rails-version: '7.0'
              
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
              
              env:
                DATABASE_URL: postgresql://postgres:postgres@localhost:5432/test
                REDIS_URL: redis://localhost:6379/0
                RAILS_ENV: test
                COVERAGE: true
              
              steps:
                - name: Checkout code
                  uses: actions/checkout@v4
                
                - name: Set up Ruby \${{ matrix.ruby-version }}
                  uses: ruby/setup-ruby@v1
                  with:
                    ruby-version: \${{ matrix.ruby-version }}
                    bundler-cache: true
                
                - name: Install dependencies
                  run: |
                    gem install rails -v \${{ matrix.rails-version }} --no-document
                    bundle install --jobs 4 --retry 3
                
                - name: Setup database
                  run: |
                    bundle exec rails db:create
                    bundle exec rails db:schema:load
                  env:
                    RAILS_ENV: test
                
                - name: Run RSpec tests
                  run: bundle exec rspec --format documentation --format json --out rspec-results.json
                
                - name: Upload RSpec results
                  uses: actions/upload-artifact@v4
                  with:
                    name: rspec-results-\${{ matrix.ruby-version }}-\${{ matrix.rails-version }}
                    path: rspec-results.json
                
                - name: Run system tests
                  run: bundle exec rails test:system
                
                - name: Upload coverage reports
                  uses: codecov/codecov-action@v3
                  with:
                    file: ./coverage/coverage.xml
                    flags: unittests
                    name: codecov-umbrella
            
            # Performance Tests
            performance:
              runs-on: ubuntu-latest
              needs: test
              
              steps:
                - name: Checkout code
                  uses: actions/checkout@v4
                
                - name: Set up Ruby
                  uses: ruby/setup-ruby@v1
                  with:
                    ruby-version: \${{ env.RUBY_VERSION }}
                    bundler-cache: true
                
                - name: Install dependencies
                  run: bundle install --jobs 4 --retry 3
                
                - name: Run performance tests
                  run: bundle exec rake performance:benchmark
                
                - name: Upload performance results
                  uses: actions/upload-artifact@v4
                  with:
                    name: performance-results
                    path: tmp/performance_results.json
            
            # Build and Deploy
            deploy:
              runs-on: ubuntu-latest
              needs: [security, test, performance]
              if: github.ref == 'refs/heads/main' || github.event_name == 'release'
              
              environment: production
              
              steps:
                - name: Checkout code
                  uses: actions/checkout@v4
                
                - name: Set up Ruby
                  uses: ruby/setup-ruby@v1
                  with:
                    ruby-version: \${{ env.RUBY_VERSION }}
                    bundler-cache: true
                
                - name: Install dependencies
                  run: bundle install --jobs 4 --retry 3 --without development test
                
                - name: Build Docker image
                  run: |
                    docker build -t my-ruby-app:\${{ github.sha }} .
                    docker tag my-ruby-app:\${{ github.sha }} my-ruby-app:latest
                
                - name: Log in to Docker Hub
                  uses: docker/login-action@v3
                  with:
                    username: \${{ secrets.DOCKER_USERNAME }}
                    password: \${{ secrets.DOCKER_PASSWORD }}
                
                - name: Push Docker image
                  run: |
                    docker push my-ruby-app:\${{ github.sha }}
                    docker push my-ruby-app:latest
                
                - name: Deploy to production
                  run: |
                    echo "Deploying to production..."
                    # Add deployment commands here
                
                - name: Run smoke tests
                  run: |
                    echo "Running smoke tests..."
                    # Add smoke test commands here
                
                - name: Notify deployment
                  uses: 8398a7/action-slack@v3
                  with:
                    status: \${{ job.status }}
                    channel: '#deployments'
                    webhook_url: \${{ secrets.SLACK_WEBHOOK }}
        YAML
        
        workflow
      end
      
      def self.generate_dockerfile
        dockerfile = <<~DOCKERFILE
          # Multi-stage build for Ruby application
          FROM ruby:3.2-alpine AS base
          
          # Install system dependencies
          RUN apk add --no-cache \\
              build-base \\
              postgresql-dev \\
              git \\
              imagemagick \\
              tzdata
          
          # Set working directory
          WORKDIR /app
          
          # Copy Gemfile and Gemfile.lock
          COPY Gemfile Gemfile.lock ./
          
          # Install Ruby dependencies
          RUN bundle config set --local deployment 'true' && \\
              bundle config set --local without 'development test' && \\
              bundle install --jobs 4 --retry 3 && \\
              bundle clean --force
          
          # Copy application code
          COPY . .
          
          # Precompile assets
          RUN bundle exec rails assets:precompile
          
          # Production stage
          FROM base AS production
          
          # Create non-root user
          RUN addgroup -g 1000 app && \\
              adduser -D -s /bin/sh -u 1000 -G app app
          
          # Change ownership of app directory
          RUN chown -R app:app /app
          
          # Switch to non-root user
          USER app
          
          # Expose port
          EXPOSE 3000
          
          # Start the application
          CMD ["bundle", "exec", "rails", "server", "-b", "0.0.0.0"]
        DOCKERFILE
        
        dockerfile
      end
      
      def self.generate_docker_compose
        docker_compose = <<~YAML
          version: '3.8'
          
          services:
            app:
              build: .
              ports:
                - "3000:3000"
              environment:
                - RAILS_ENV=production
                - DATABASE_URL=postgresql://postgres:password@db:5432/myapp_production
                - REDIS_URL=redis://redis:6379/0
              depends_on:
                - db
                - redis
              volumes:
                - rails_storage:/app/storage
              restart: unless-stopped
            
            db:
              image: postgres:15-alpine
              environment:
                - POSTGRES_DB=myapp_production
                - POSTGRES_USER=postgres
                - POSTGRES_PASSWORD=password
              volumes:
                - postgres_data:/var/lib/postgresql/data
              restart: unless-stopped
            
            redis:
              image: redis:7-alpine
              volumes:
                - redis_data:/data
              restart: unless-stopped
            
            nginx:
              image: nginx:alpine
              ports:
                - "80:80"
                - "443:443"
              volumes:
                - ./nginx.conf:/etc/nginx/nginx.conf
                - ./ssl:/etc/nginx/ssl
              depends_on:
                - app
              restart: unless-stopped
          
          volumes:
            postgres_data:
            redis_data:
            rails_storage:
        YAML
        
        docker_compose
      end
    end
    
    # 2. GitLab CI/CD Pipeline
    # Complete CI/CD pipeline using GitLab CI
    
    class GitLabCIPipeline
      def self.generate_gitlab_ci
        gitlab_ci = <<~YAML
          # GitLab CI/CD Configuration
          stages:
            - security
            - test
            - performance
            - build
            - deploy
          
          variables:
            RUBY_VERSION: "3.2"
            RAILS_ENV: test
            POSTGRES_DB: test
            POSTGRES_USER: postgres
            POSTGRES_PASSWORD: ""
          
          # Security scanning
          security-scan:
            stage: security
            image: ruby:3.2-alpine
            before_script:
              - apk add --no-cache postgresql-dev git
              - gem install brakeman bundler-audit
            script:
              - brakeman --exit-on-warn --format json --output-report brakeman-report.json
              - bundle audit --update --format json --output audit-report.json
            artifacts:
              reports:
                sast: brakeman-report.json
                dependency_scanning: audit-report.json
              expire_in: 1 week
            allow_failure: true
          
          # Test matrix
          test:
            stage: test
            image: ruby:$RUBY_VERSION
            services:
              - postgres:15-alpine
              - redis:7-alpine
            variables:
              DATABASE_URL: "postgresql://$POSTGRES_USER:$POSTGRES_PASSWORD@postgres:5432/$POSTGRES_DB"
              REDIS_URL: "redis://redis:6379/0"
            parallel:
              matrix:
                - RUBY_VERSION: ["3.0", "3.1", "3.2"]
            before_script:
              - apt-get update -qq && apt-get install -y postgresql-client
              - ruby -v
              - gem install bundler
              - bundle config set --local deployment 'true'
              - bundle config set --local without 'development production'
              - bundle install --jobs $(nproc) --retry 3
            script:
              - bundle exec rails db:create
              - bundle exec rails db:schema:load
              - bundle exec rspec --format documentation --format json --out rspec-results.json
              - bundle exec rails test:system
            artifacts:
              reports:
                junit: rspec-results.json
              expire_in: 1 week
            coverage: '/Coverage: \d+\.\d+%/'
          
          # Performance tests
          performance:
            stage: performance
            image: ruby:3.2-alpine
            dependencies:
              - test
            before_script:
              - apk add --no-cache postgresql-dev
              - bundle config set --local deployment 'true'
              - bundle config set --local without 'development test'
              - bundle install --jobs $(nproc) --retry 3
            script:
              - bundle exec rake performance:benchmark
            artifacts:
              reports:
                performance: tmp/performance_results.json
              expire_in: 1 week
            only:
              - main
          
          # Build Docker image
          build:
            stage: build
            image: docker:20.10.16
            services:
              - docker:20.10.16-dind
            variables:
              DOCKER_TLS_CERTDIR: "/certs"
              DOCKER_DRIVER: overlay2
            before_script:
              - docker login -u $CI_REGISTRY_USER -p $CI_REGISTRY_PASSWORD $CI_REGISTRY
            script:
              - docker build -t $CI_REGISTRY_IMAGE:$CI_COMMIT_SHA .
              - docker build -t $CI_REGISTRY_IMAGE:latest .
              - docker push $CI_REGISTRY_IMAGE:$CI_COMMIT_SHA
              - docker push $CI_REGISTRY_IMAGE:latest
            only:
              - main
              - tags
          
          # Deploy to staging
          deploy-staging:
            stage: deploy
            image: alpine:latest
            before_script:
              - apk add --no-cache openssh-client
              - eval $(ssh-agent -s)
              - ssh-add <(echo "$STAGING_SSH_PRIVATE_KEY")
              - mkdir -p ~/.ssh
              - '[[ -f /.dockerenv ]] && echo -e "Host *\\n\\tStrictHostKeyChecking no\\n\\n" > ~/.ssh/config'
            script:
              - ssh $STAGING_USER@$STAGING_HOST "cd /var/www/myapp && docker-compose pull && docker-compose up -d"
            environment:
              name: staging
              url: https://staging.myapp.com
            only:
              - main
            when: manual
          
          # Deploy to production
          deploy-production:
            stage: deploy
            image: alpine:latest
            before_script:
              - apk add --no-cache openssh-client
              - eval $(ssh-agent -s)
              - ssh-add <(echo "$PRODUCTION_SSH_PRIVATE_KEY")
              - mkdir -p ~/.ssh
              - '[[ -f /.dockerenv ]] && echo -e "Host *\\n\\tStrictHostKeyChecking no\\n\\n" > ~/.ssh/config'
            script:
              - ssh $PRODUCTION_USER@$PRODUCTION_HOST "cd /var/www/myapp && docker-compose pull && docker-compose up -d"
            environment:
              name: production
              url: https://myapp.com
            only:
              - tags
            when: manual
          
          # Deploy notifications
          notify-deployment:
            stage: deploy
            image: alpine:latest
            before_script:
              - apk add --no-cache curl
            script:
              - |
                curl -X POST -H 'Content-type: application/json' \\
                  --data '{"text":"🚀 Deployed $CI_COMMIT_REF_NAME to $CI_ENVIRONMENT_NAME ($CI_COMMIT_SHA)"}' \\
                  "$SLACK_WEBHOOK_URL"
            when: on_success
            only:
              - main
              - tags
        YAML
        
        gitlab_ci
      end
    end
    
    # 3. Jenkins Pipeline
    # Complete CI/CD pipeline using Jenkins
    
    class JenkinsPipeline
      def self.generate_jenkinsfile
        jenkinsfile = <<~GROOVY
          pipeline {
              agent any
              
              environment {
                  RUBY_VERSION = '3.2'
                  RAILS_ENV = 'test'
                  DOCKER_REGISTRY = 'your-registry.com'
                  APP_NAME = 'my-ruby-app'
              }
              
              stages {
                  stage('Checkout') {
                      steps {
                          checkout scm
                      }
                  }
                  
                  stage('Setup') {
                      steps {
                          script {
                              // Set up Ruby environment
                              sh """
                                  rbenv install \${RUBY_VERSION} || true
                                  rbenv global \${RUBY_VERSION}
                                  ruby -v
                              """
                              
                              // Install dependencies
                              sh """
                                  gem install bundler
                                  bundle config set --local deployment 'true'
                                  bundle config set --local without 'development production'
                                  bundle install --jobs 4 --retry 3
                              """
                          }
                      }
                  }
                  
                  stage('Security Scan') {
                      parallel {
                          stage('Brakeman') {
                              steps {
                                  sh 'bundle exec brakeman --exit-on-warn --format json --output-report brakeman-report.json'
                                  publishHTML([
                                      allowMissing: false,
                                      alwaysLinkToLastBuild: true,
                                      keepAll: true,
                                      reportDir: '.',
                                      reportFiles: 'brakeman-report.json',
                                      reportName: 'Brakeman Security Report',
                                      reportTitles: 'Brakeman Security Scan Results'
                                  ])
                              }
                          }
                          
                          stage('Bundle Audit') {
                              steps {
                                  sh 'bundle audit --update --format json --output audit-report.json'
                                  publishHTML([
                                      allowMissing: false,
                                      alwaysLinkToLastBuild: true,
                                      keepAll: true,
                                      reportDir: '.',
                                      reportFiles: 'audit-report.json',
                                      reportName: 'Bundle Audit Report',
                                      reportTitles: 'Bundle Audit Results'
                                  ])
                              }
                          }
                      }
                  }
                  
                  stage('Test') {
                      parallel {
                          stage('Unit Tests') {
                              steps {
                                  sh """
                                      bundle exec rails db:create RAILS_ENV=test
                                      bundle exec rails db:schema:load RAILS_ENV=test
                                      bundle exec rspec --format documentation --format json --out rspec-results.json
                                  """
                                  
                                  publishTestResults testResultsPattern: 'rspec-results.json'
                              }
                          }
                          
                          stage('System Tests') {
                              steps {
                                  sh 'bundle exec rails test:system'
                              }
                          }
                      }
                  }
                  
                  stage('Performance') {
                      steps {
                          sh 'bundle exec rake performance:benchmark'
                          
                          publishHTML([
                              allowMissing: false,
                              alwaysLinkToLastBuild: true,
                              keepAll: true,
                              reportDir: 'tmp',
                              reportFiles: 'performance_results.json',
                              reportName: 'Performance Report',
                              reportTitles: 'Performance Benchmark Results'
                          ])
                      }
                  }
                  
                  stage('Build') {
                      when {
                          branch 'main'
                      }
                      steps {
                          script {
                              def dockerImage = "\${DOCKER_REGISTRY}/\${APP_NAME}:\${BUILD_NUMBER}"
                              
                              // Build Docker image
                              sh "docker build -t \${dockerImage} ."
                              sh "docker tag \${dockerImage} \${DOCKER_REGISTRY}/\${APP_NAME}:latest"
                              
                              // Push to registry
                              withDockerRegistry([credentialsId: 'docker-registry']) {
                                  sh "docker push \${dockerImage}"
                                  sh "docker push \${DOCKER_REGISTRY}/\${APP_NAME}:latest"
                              }
                              
                              // Store image name for deployment
                              env.DOCKER_IMAGE = dockerImage
                          }
                      }
                  }
                  
                  stage('Deploy Staging') {
                      when {
                          branch 'main'
                      }
                      steps {
                          script {
                              // Deploy to staging environment
                              sshagent(['staging-ssh-key']) {
                                  sh """
                                      ssh staging@staging-server.com \\
                                      'cd /var/www/myapp && \\
                                       docker-compose pull && \\
                                       docker-compose up -d'
                                  """
                              }
                              
                              // Run smoke tests
                              sh 'bundle exec rake smoke:test'
                          }
                      }
                  }
                  
                  stage('Deploy Production') {
                      when {
                          tag pattern: "v\\d+\\.\\d+\\.\\d+", comparator: "REGEXP"
                      }
                      input {
                          message "Deploy to production?"
                          ok "Deploy"
                      }
                      steps {
                          script {
                              // Deploy to production environment
                              sshagent(['production-ssh-key']) {
                                  sh """
                                      ssh production@production-server.com \\
                                      'cd /var/www/myapp && \\
                                       docker-compose pull && \\
                                       docker-compose up -d'
                                  """
                              }
                              
                              // Run health checks
                              sh 'bundle exec rake health:check'
                          }
                      }
                  }
              }
              
              post {
                  always {
                      // Clean up workspace
                      cleanWs()
                  }
                  
                  success {
                      // Notify success
                      slackSend(
                          channel: '#deployments',
                          color: 'good',
                          message: "✅ Deployment successful: \${env.JOB_NAME} - \${env.BUILD_NUMBER}"
                      )
                  }
                  
                  failure {
                      // Notify failure
                      slackSend(
                          channel: '#deployments',
                          color: 'danger',
                          message: "❌ Deployment failed: \${env.JOB_NAME} - \${env.BUILD_NUMBER}"
                      )
                  }
                  
                  unstable {
                      // Notify unstable build
                      slackSend(
                          channel: '#deployments',
                          color: 'warning',
                          message: "⚠️ Deployment unstable: \${env.JOB_NAME} - \${env.BUILD_NUMBER}"
                      )
                  }
              }
          }
        GROOVY
        
        jenkinsfile
      end
    end
    
    # 4. Azure DevOps Pipeline
    # Complete CI/CD pipeline using Azure DevOps
    
    class AzureDevOpsPipeline
      def self.generate_azure_pipeline
        azure_pipeline = <<~YAML
          # Azure DevOps Pipeline for Ruby Application
          trigger:
            branches:
              include:
                - main
                - develop
            tags:
              include:
                - v*
          
          pr:
            branches:
              include:
                - main
          
          variables:
            rubyVersion: '3.2'
            railsEnv: 'test'
            imageName: 'my-ruby-app'
            dockerRegistryServiceConnection: 'docker-hub'
            containerRegistry: 'mydockerhub'
          
          stages:
          - stage: Security
            displayName: 'Security Scanning'
            jobs:
            - job: SecurityScan
              displayName: 'Run Security Scans'
              pool:
                vmImage: 'ubuntu-latest'
              steps:
              - task: UseRubyVersion@0
                inputs:
                  versionSpec: '$(rubyVersion)'
                  addToPath: true
              
              - script: |
                  gem install brakeman bundler-audit
                  brakeman --exit-on-warn --format json --output-report brakeman-report.json
                  bundle audit --update --format json --output audit-report.json
                displayName: 'Run Security Tools'
              
              - task: PublishBuildArtifacts@1
                inputs:
                  pathToPublish: 'brakeman-report.json'
                  artifactName: 'brakeman-report'
                displayName: 'Publish Brakeman Report'
              
              - task: PublishBuildArtifacts@1
                inputs:
                  pathToPublish: 'audit-report.json'
                  artifactName: 'audit-report'
                displayName: 'Publish Audit Report'
          
          - stage: Test
            displayName: 'Testing'
            dependsOn: Security
            strategy:
              matrix:
                Ruby30:
                  rubyVersion: '3.0'
                Ruby31:
                  rubyVersion: '3.1'
                Ruby32:
                  rubyVersion: '3.2'
            jobs:
            - job: Test
              displayName: 'Run Tests'
              pool:
                vmImage: 'ubuntu-latest'
              services:
                postgres:
                  image: postgres:15
                  env:
                    POSTGRES_PASSWORD: postgres
                  ports:
                    - 5432:5432
                redis:
                  image: redis:7
                  ports:
                    - 6379:6379
              variables:
                DATABASE_URL: postgresql://postgres:postgres@localhost:5432/test
                REDIS_URL: redis://localhost:6379/0
              steps:
              - task: UseRubyVersion@0
                inputs:
                  versionSpec: '$(rubyVersion)'
                  addToPath: true
              
              - script: |
                  gem install bundler
                  bundle config set --local deployment 'true'
                  bundle config set --local without 'development production'
                  bundle install --jobs 4 --retry 3
                displayName: 'Install Dependencies'
              
              - script: |
                  bundle exec rails db:create
                  bundle exec rails db:schema:load
                displayName: 'Setup Database'
                env:
                  RAILS_ENV: test
              
              - script: |
                  bundle exec rspec --format documentation --format json --out rspec-results.json
                displayName: 'Run RSpec Tests'
              
              - task: PublishTestResults@2
                inputs:
                  testResultsFiles: 'rspec-results.json'
                  testRunTitle: 'RSpec Tests'
                displayName: 'Publish Test Results'
              
              - script: |
                  bundle exec rails test:system
                displayName: 'Run System Tests'
          
          - stage: Build
            displayName: 'Build Docker Image'
            dependsOn: Test
            condition: and(succeeded(), eq(variables['Build.SourceBranch'], 'refs/heads/main'))
            jobs:
            - job: Build
              displayName: 'Build and Push Docker Image'
              pool:
                vmImage: 'ubuntu-latest'
              steps:
              - task: Docker@2
                displayName: 'Build Docker Image'
                inputs:
                  containerRegistry: '$(dockerRegistryServiceConnection)'
                  repository: '$(containerRegistry)/$(imageName)'
                  command: 'build'
                  Dockerfile: 'Dockerfile'
                  tags: |
                    $(Build.BuildNumber)
                    latest
              
              - task: Docker@2
                displayName: 'Push Docker Image'
                inputs:
                  containerRegistry: '$(dockerRegistryServiceConnection)'
                  repository: '$(containerRegistry)/$(imageName)'
                  command: 'push'
                  tags: |
                    $(Build.BuildNumber)
                    latest
          
          - stage: DeployStaging
            displayName: 'Deploy to Staging'
            dependsOn: Build
            condition: and(succeeded(), eq(variables['Build.SourceBranch'], 'refs/heads/main'))
            jobs:
            - deployment: DeployStaging
              displayName: 'Deploy to Staging Environment'
              environment: 'staging'
              strategy:
                runOnce:
                  deploy:
                    steps:
                    - task: AzureWebApp@1
                      displayName: 'Deploy to Azure Web App'
                      inputs:
                        azureSubscription: 'azure-subscription'
                        appType: 'webAppLinux'
                        webAppName: 'my-ruby-app-staging'
                        package: '$(Pipeline.Workspace)/$(imageName)'
                        runtimeStack: 'RUBY|3.2'
                        startupCommand: 'bundle exec rails server -b 0.0.0.0 -p 80'
                    
                    - task: Bash@3
                      displayName: 'Run Smoke Tests'
                      inputs:
                        targetType: 'inline'
                        script: |
                          curl -f https://my-ruby-app-staging.azurewebsites.net/health || exit 1
          
          - stage: DeployProduction
            displayName: 'Deploy to Production'
            dependsOn: Build
            condition: and(succeeded(), startsWith(variables['Build.SourceBranch'], 'refs/tags/'))
            jobs:
            - deployment: DeployProduction
              displayName: 'Deploy to Production Environment'
              environment: 'production'
              strategy:
                runOnce:
                  deploy:
                    steps:
                    - task: AzureWebApp@1
                      displayName: 'Deploy to Azure Web App'
                      inputs:
                        azureSubscription: 'azure-subscription'
                        appType: 'webAppLinux'
                        webAppName: 'my-ruby-app-production'
                        package: '$(Pipeline.Workspace)/$(imageName)'
                        runtimeStack: 'RUBY|3.2'
                        startupCommand: 'bundle exec rails server -b 0.0.0.0 -p 80'
                    
                    - task: Bash@3
                      displayName: 'Run Health Checks'
                      inputs:
                        targetType: 'inline'
                        script: |
                          curl -f https://my-ruby-app-production.azurewebsites.net/health || exit 1
        YAML
        
        azure_pipeline
      end
    end
    
    # 5. Deployment Strategies
    # Different deployment strategies and implementations
    
    class DeploymentStrategies
      def self.blue_green_deployment
        strategy = <<~RUBY
          # Blue-Green Deployment Strategy
          class BlueGreenDeployment
            def initialize(app_name, environment)
              @app_name = app_name
              @environment = environment
              @current_color = determine_current_color
              @new_color = @current_color == :blue ? :green : :blue
            end
            
            def deploy(new_image)
              puts "Starting #{@new_color} deployment..."
              
              # Deploy to inactive environment
              deploy_to_environment(@new_color, new_image)
              
              # Run health checks
              if health_checks_pass?(@new_color)
                # Switch traffic
                switch_traffic(@new_color)
                
                # Clean up old environment
                cleanup_environment(@current_color)
                
                # Update current color
                @current_color = @new_color
                
                puts "Deployment completed successfully!"
                true
              else
                puts "Health checks failed, rolling back..."
                rollback_deployment
                false
              end
            end
            
            def rollback_deployment
              puts "Rolling back to #{@current_color}..."
              switch_traffic(@current_color)
              cleanup_environment(@new_color)
            end
            
            private
            
            def determine_current_color
              # Check which environment is currently active
              active_env = get_active_environment
              active_env == 'blue' ? :blue : :green
            end
            
            def deploy_to_environment(color, image)
              env_name = "#{@app_name}-#{color}"
              
              puts "Deploying to #{env_name}..."
              
              # Update Docker service
              system("docker service update --image #{image} #{env_name}")
              
              # Wait for deployment to complete
              wait_for_deployment(env_name)
            end
            
            def health_checks_pass?(color)
              env_url = get_environment_url(color)
              
              # Run health checks
              health_checks = [
                check_http_status(env_url, 200),
                check_database_connection,
                check_redis_connection,
                run_smoke_tests(env_url)
              ]
              
              health_checks.all?
            end
            
            def switch_traffic(color)
              puts "Switching traffic to #{color}..."
              
              # Update load balancer
              update_load_balancer(color)
              
              # Update DNS if needed
              update_dns(color)
            end
            
            def cleanup_environment(color)
              env_name = "#{@app_name}-#{color}"
              puts "Cleaning up #{env_name}..."
              
              # Scale down old environment
              system("docker service scale #{env_name}=0")
              
              # Remove old containers
              system("docker service rm #{env_name}")
            end
            
            def get_active_environment
              # Get current active environment from load balancer
              system("curl -s http://load-balancer/active-environment").chomp
            end
            
            def get_environment_url(color)
              host = "#{@app_name}-#{color}.#{@environment}.com"
              "https://#{host}"
            end
            
            def check_http_status(url, expected_status)
              response = HTTParty.get(url)
              response.code == expected_status
            end
            
            def check_database_connection
              # Check database connectivity
              ActiveRecord::Base.connection.execute("SELECT 1")
              true
            rescue
              false
            end
            
            def check_redis_connection
              # Check Redis connectivity
              Redis.new.ping
              true
            rescue
              false
            end
            
            def run_smoke_tests(url)
              # Run smoke tests
              system("bundle exec rake smoke:test URL=#{url}")
              $?.success?
            end
            
            def update_load_balancer(color)
              # Update load balancer configuration
              config = {
                active_environment: color.to_s,
                updated_at: Time.current
              }
              
              File.write('/etc/load-balancer/config.json', config.to_json)
              system("systemctl reload nginx")
            end
            
            def update_dns(color)
              # Update DNS records
              system("curl -X POST -H 'Content-Type: application/json' \\
                     -d '{\"environment\":\"#{color}\"}' \\
                     https://dns-api/update")
            end
            
            def wait_for_deployment(env_name, timeout = 300)
              start_time = Time.current
              
              loop do
                break if deployment_ready?(env_name)
                break if Time.current - start_time > timeout
                
                sleep 5
              end
            end
            
            def deployment_ready?(env_name)
              # Check if deployment is ready
              system("docker service ps #{env_name} | grep -q 'Running'")
            end
          end
        RUBY
        
        strategy
      end
      
      def self.canary_deployment
        strategy = <<~RUBY
          # Canary Deployment Strategy
          class CanaryDeployment
            def initialize(app_name, environment)
              @app_name = app_name
              @environment = environment
              @canary_percentage = 0
              @max_canary_percentage = 50
            end
            
            def deploy(new_image, options = {})
              @canary_percentage = options[:canary_percentage] || 10
              @max_canary_percentage = options[:max_canary_percentage] || 50
              
              puts "Starting canary deployment with #{@canary_percentage}% traffic..."
              
              # Deploy canary version
              deploy_canary(new_image)
              
              # Gradually increase traffic
              success = gradual_rollout(new_image)
              
              if success
                # Full rollout
                full_rollout(new_image)
                true
              else
                # Rollback
                rollback_deployment
                false
              end
            end
            
            private
            
            def deploy_canary(image)
              canary_service = "#{@app_name}-canary"
              
              puts "Deploying canary version..."
              
              # Deploy canary service
              system("docker service create --name #{canary_service} #{image}")
              
              # Add canary to load balancer with small percentage
              update_load_balancer(canary_percentage: @canary_percentage)
            end
            
            def gradual_rollout(image)
              while @canary_percentage < @max_canary_percentage
                puts "Current canary traffic: #{@canary_percentage}%"
                
                # Monitor metrics
                if canary_healthy?
                  # Increase traffic
                  @canary_percentage = [@canary_percentage + 10, @max_canary_percentage].min
                  update_load_balancer(canary_percentage: @canary_percentage)
                  
                  # Wait for metrics to stabilize
                  sleep 60
                else
                  puts "Canary deployment unhealthy, stopping rollout"
                  return false
                end
              end
              
              true
            end
            
            def canary_healthy?
              metrics = collect_metrics
              
              # Check error rate
              return false if metrics[:error_rate] > 1.0
              
              # Check response time
              return false if metrics[:response_time] > 500
              
              # Check throughput
              return false if metrics[:throughput] < baseline_throughput * 0.9
              
              true
            end
            
            def full_rollout(image)
              puts "Starting full rollout..."
              
              # Deploy new version to all instances
              system("docker service update --image #{image} #{@app_name}")
              
              # Set traffic to 100%
              update_load_balancer(canary_percentage: 100)
              
              # Remove canary service
              system("docker service rm #{@app_name}-canary")
              
              puts "Full rollout completed!"
            end
            
            def rollback_deployment
              puts "Rolling back deployment..."
              
              # Remove canary service
              system("docker service rm #{@app_name}-canary")
              
              # Reset traffic to 0% canary
              update_load_balancer(canary_percentage: 0)
              
              puts "Rollback completed!"
            end
            
            def update_load_balancer(options = {})
              config = {
                canary_percentage: options[:canary_percentage] || 0,
                updated_at: Time.current
              }
              
              File.write('/etc/load-balancer/canary-config.json', config.to_json)
              system("systemctl reload nginx")
            end
            
            def collect_metrics
              # Collect metrics from monitoring system
              {
                error_rate: 0.5,
                response_time: 200,
                throughput: 1000
              }
            end
            
            def baseline_throughput
              # Get baseline throughput from historical data
              1000
            end
          end
        RUBY
        
        strategy
      end
      
      def self.rolling_deployment
        strategy = <<~RUBY
          # Rolling Deployment Strategy
          class RollingDeployment
            def initialize(app_name, environment)
              @app_name = app_name
              @environment = environment
              @batch_size = 2
              @health_check_timeout = 30
            end
            
            def deploy(new_image, options = {})
              @batch_size = options[:batch_size] || 2
              
              puts "Starting rolling deployment with batch size #{@batch_size}..."
              
              # Get current service
              service = get_service_info
              
              # Update service with rolling update
              success = rolling_update(service, new_image)
              
              if success
                puts "Rolling deployment completed successfully!"
                true
              else
                puts "Rolling deployment failed!"
                false
              end
            end
            
            private
            
            def rolling_update(service, image)
              # Configure rolling update
              update_config = {
                image: image,
                update_config: {
                  parallelism: @batch_size,
                  delay: '10s',
                  failure_action: 'rollback',
                  monitor: '30s',
                  max_failure_ratio: '0.1'
                }
              }
              
              # Apply rolling update
              system("docker service update \\
                     --update-parallelism #{@batch_size} \\
                     --update-delay 10s \\
                     --update-failure-action rollback \\
                     --update-monitor 30s \\
                     --update-max-failure-ratio 0.1 \\
                     #{service[:name]} #{image}")
              
              # Monitor update progress
              monitor_update_progress(service[:name])
            end
            
            def monitor_update_progress(service_name)
              puts "Monitoring update progress..."
              
              loop do
                status = get_service_status(service_name)
                
                case status[:state]
                when 'completed'
                  puts "Update completed successfully!"
                  return true
                when 'failed'
                  puts "Update failed!"
                  return false
                when 'paused'
                  puts "Update paused, waiting..."
                  sleep 10
                else
                  puts "Update in progress: #{status[:progress]}%"
                  sleep 5
                end
              end
            end
            
            def get_service_info
              # Get current service information
              {
                name: @app_name,
                replicas: 5,
                image: 'old-image'
              }
            end
            
            def get_service_status(service_name)
              # Get service update status
              {
                state: 'completed',
                progress: 100
              }
            end
          end
        RUBY
        
        strategy
      end
    end
    
    # 6. Monitoring and Alerting
    # Comprehensive monitoring and alerting setup
    
    class MonitoringAndAlerting
      def self.generate_prometheus_config
        config = <<~YAML
          global:
            scrape_interval: 15s
            evaluation_interval: 15s
          
          rule_files:
            - "ruby_app_rules.yml"
          
          alerting:
            alertmanagers:
              - static_configs:
                  - targets:
                    - alertmanager:9093
          
          scrape_configs:
            - job_name: 'ruby-app'
              static_configs:
                - targets: ['localhost:3000']
              metrics_path: '/metrics'
              scrape_interval: 10s
              scrape_timeout: 5s
              params:
                format: ['prometheus']
            
            - job_name: 'postgres'
              static_configs:
                - targets: ['postgres-exporter:9187']
              scrape_interval: 15s
            
            - job_name: 'redis'
              static_configs:
                - targets: ['redis-exporter:9121']
              scrape_interval: 15s
            
            - job_name: 'nginx'
              static_configs:
                - targets: ['nginx-exporter:9113']
              scrape_interval: 15s
            
            - job_name: 'node'
              static_configs:
                - targets: ['node-exporter:9100']
              scrape_interval: 15s
        YAML
        
        config
      end
      
      def self.generate_grafana_dashboard
        dashboard = <<~JSON
          {
            "dashboard": {
              "title": "Ruby Application Dashboard",
              "tags": ["ruby", "rails", "production"],
              "timezone": "browser",
              "panels": [
                {
                  "title": "Request Rate",
                  "type": "graph",
                  "targets": [
                    {
                      "expr": "rate(http_requests_total[5m])",
                      "legendFormat": "{{method}} {{status}}"
                    }
                  ],
                  "gridPos": {"x": 0, "y": 0, "w": 12, "h": 8}
                },
                {
                  "title": "Response Time",
                  "type": "graph",
                  "targets": [
                    {
                      "expr": "histogram_quantile(0.95, rate(http_request_duration_seconds_bucket[5m]))",
                      "legendFormat": "95th percentile"
                    },
                    {
                      "expr": "histogram_quantile(0.50, rate(http_request_duration_seconds_bucket[5m]))",
                      "legendFormat": "50th percentile"
                    }
                  ],
                  "gridPos": {"x": 12, "y": 0, "w": 12, "h": 8}
                },
                {
                  "title": "Error Rate",
                  "type": "stat",
                  "targets": [
                    {
                      "expr": "rate(http_requests_total{status=~\"5..\"}[5m]) / rate(http_requests_total[5m]) * 100",
                      "legendFormat": "Error Rate %"
                    }
                  ],
                  "gridPos": {"x": 0, "y": 8, "w": 6, "h": 8}
                },
                {
                  "title": "Memory Usage",
                  "type": "graph",
                  "targets": [
                    {
                      "expr": "process_resident_memory_bytes",
                      "legendFormat": "Memory Usage"
                    }
                  ],
                  "gridPos": {"x": 6, "y": 8, "w": 6, "h": 8}
                },
                {
                  "title": "CPU Usage",
                  "type": "graph",
                  "targets": [
                    {
                      "expr": "rate(process_cpu_seconds_total[5m]) * 100",
                      "legendFormat": "CPU Usage %"
                    }
                  ],
                  "gridPos": {"x": 12, "y": 8, "w": 6, "h": 8}
                },
                {
                  "title": "Database Connections",
                  "type": "graph",
                  "targets": [
                    {
                      "expr": "pg_stat_database_numbackends",
                      "legendFormat": "Active Connections"
                    }
                  ],
                  "gridPos": {"x": 18, "y": 8, "w": 6, "h": 8}
                }
              ],
              "time": {
                "from": "now-1h",
                "to": "now"
              },
              "refresh": "5s"
            }
          }
        JSON
        
        dashboard
      end
      
      def self.generate_alert_rules
        rules = <<~YAML
          groups:
            - name: ruby_app_alerts
              rules:
                - alert: HighErrorRate
                  expr: rate(http_requests_total{status=~"5.."}[5m]) / rate(http_requests_total[5m]) * 100 > 5
                  for: 5m
                  labels:
                    severity: critical
                  annotations:
                    summary: "High error rate detected"
                    description: "Error rate is {{ $value | printf \"%.2f\" }}% for the last 5 minutes"
                
                - alert: HighResponseTime
                  expr: histogram_quantile(0.95, rate(http_request_duration_seconds_bucket[5m])) > 1
                  for: 5m
                  labels:
                    severity: warning
                  annotations:
                    summary: "High response time detected"
                    description: "95th percentile response time is {{ $value }}s"
                
                - alert: HighMemoryUsage
                  expr: process_resident_memory_bytes / (1024*1024*1024) > 1
                  for: 10m
                  labels:
                    severity: warning
                  annotations:
                    summary: "High memory usage detected"
                    description: "Memory usage is {{ $value | printf \"%.2f\" }}GB"
                
                - alert: HighCPUUsage
                  expr: rate(process_cpu_seconds_total[5m]) * 100 > 80
                  for: 5m
                  labels:
                    severity: warning
                  annotations:
                    summary: "High CPU usage detected"
                    description: "CPU usage is {{ $value | printf \"%.2f\" }}%"
                
                - alert: DatabaseConnectionHigh
                  expr: pg_stat_database_numbackends > 80
                  for: 5m
                  labels:
                    severity: warning
                  annotations:
                    summary: "High database connection count"
                    description: "Database has {{ $value }} active connections"
                
                - alert: ServiceDown
                  expr: up{job="ruby-app"} == 0
                  for: 1m
                  labels:
                    severity: critical
                  annotations:
                    summary: "Service is down"
                    description: "Ruby application is not responding"
        YAML
        
        rules
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  puts "CI/CD Pipelines Demonstration"
  puts "=" * 60
  
  # Demonstrate different CI/CD platforms
  puts "\n1. CI/CD Platforms:"
  puts "✅ GitHub Actions"
  puts "✅ GitLab CI"
  puts "✅ Jenkins"
  puts "✅ Azure DevOps"
  
  # Demonstrate deployment strategies
  puts "\n2. Deployment Strategies:"
  puts "✅ Blue-Green Deployment"
  puts "✅ Canary Deployment"
  puts "✅ Rolling Deployment"
  puts "✅ Feature Flags"
  
  # Demonstrate monitoring
  puts "\n3. Monitoring and Alerting:"
  puts "✅ Prometheus configuration"
  puts "✅ Grafana dashboards"
  puts "✅ Alert rules"
  puts "✅ Health checks"
  
  # Demonstrate security
  puts "\n4. Security Integration:"
  puts "✅ Static analysis"
  puts "✅ Dependency scanning"
  puts "✅ Container security"
  puts "✅ Secrets management"
  
  # Demonstrate automation
  puts "\n5. Automation Features:"
  puts "✅ Parallel testing"
  puts "✅ Matrix builds"
  puts "✅ Artifact management"
  puts "✅ Environment provisioning"
  
  puts "\nCI/CD pipelines automate the entire software delivery lifecycle!"
end
