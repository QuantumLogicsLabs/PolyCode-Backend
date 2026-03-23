# DevOps with Ruby

## Overview

DevOps combines software development and IT operations to shorten the development lifecycle and provide continuous delivery with high software quality. Ruby has excellent tools for automation, testing, deployment, and monitoring.

## Continuous Integration

### Jenkins Pipeline

```groovy
// Jenkinsfile for Ruby application
pipeline {
    agent any
    
    tools {
        ruby '3.1.0'
        bundler '2.3.0'
    }
    
    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }
        
        stage('Setup') {
            steps {
                sh 'bundle install --deployment --without development test'
            }
        }
        
        stage('Lint') {
            steps {
                sh 'bundle exec rubocop'
            }
        }
        
        stage('Test') {
            parallel {
                stage('Unit Tests') {
                    steps {
                        sh 'bundle exec rspec spec/unit/'
                    }
                }
                
                stage('Integration Tests') {
                    steps {
                        sh 'bundle exec rspec spec/integration/'
                    }
                }
                
                stage('System Tests') {
                    steps {
                        sh 'bundle exec rspec spec/system/'
                    }
                }
            }
        }
        
        stage('Security Scan') {
            steps {
                sh 'bundle exec brakeman --exit-code 0'
                sh 'bundle audit update'
                sh 'bundle audit check'
            }
        }
        
        stage('Build') {
            steps {
                sh 'bundle exec rails assets:precompile'
                sh 'docker build -t myapp:$BUILD_NUMBER .'
            }
        }
        
        stage('Deploy to Staging') {
            steps {
                sh 'kubectl apply -f k8s/staging/'
                sh 'kubectl rollout status deployment/myapp-staging'
            }
        }
        
        stage('Deploy to Production') {
            when {
                branch 'main'
            }
            steps {
                sh 'kubectl apply -f k8s/production/'
                sh 'kubectl rollout status deployment/myapp-production'
            }
        }
    }
    
    post {
        always {
            junit 'test-results/**/*.xml'
            cobertura 'coverage/coverage.xml'
        }
        
        success {
            slackSend(
                channel: '#deployments',
                color: 'good',
                message: "✅ Deployment successful: ${env.JOB_NAME} - ${env.BUILD_NUMBER}"
            )
        }
        
        failure {
            slackSend(
                channel: '#deployments',
                color: 'danger',
                message: "❌ Deployment failed: ${env.JOB_NAME} - ${env.BUILD_NUMBER}"
            )
        }
    }
}
```

### GitHub Actions

```yaml
# .github/workflows/ci.yml
name: CI/CD Pipeline

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        ruby-version: ['2.7', '3.0', '3.1']
    
    services:
      postgres:
        image: postgres:13
        env:
          POSTGRES_PASSWORD: postgres
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
        ports:
          - 5432:5432
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Set up Ruby
      uses: ruby/setup-ruby@v1
      with:
        ruby-version: ${{ matrix.ruby-version }}
        bundler-cache: true
    
    - name: Install dependencies
      run: |
        gem install bundler
        bundle config set --local without 'development test'
        bundle install --jobs 4 --retry 3
    
    - name: Set up database
      run: |
        cp config/database.yml.ci config/database.yml
        bundle exec rails db:create
        bundle exec rails db:migrate
      env:
        RAILS_ENV: test
        DATABASE_URL: postgres://postgres:postgres@localhost:5432/test
    
    - name: Run linter
      run: bundle exec rubocop
    
    - name: Run security checks
      run: |
        bundle exec brakeman --exit-code 0
        bundle exec bundle-audit check
    
    - name: Run tests
      run: bundle exec rspec
      env:
        RAILS_ENV: test
        DATABASE_URL: postgres://postgres:postgres@localhost:5432/test
    
    - name: Upload coverage
      uses: codecov/codecov-action@v3
      with:
        token: ${{ secrets.CODECOV_TOKEN }}

  build:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Set up Docker Buildx
      uses: docker/setup-buildx-action@v2
    
    - name: Login to Docker Hub
      uses: docker/login-action@v2
      with:
        username: ${{ secrets.DOCKER_USERNAME }}
        password: ${{ secrets.DOCKER_PASSWORD }}
    
    - name: Build and push Docker image
      uses: docker/build-push-action@v4
      with:
        context: .
        file: ./Dockerfile
        push: true
        tags: |
          yourusername/myapp:latest
          yourusername/myapp:${{ github.sha }}

  deploy:
    needs: build
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Deploy to production
      run: |
        echo "Deploying to production..."
        # Add your deployment commands here
```

## Containerization

### Dockerfile

```dockerfile
# Dockerfile for Rails application
FROM ruby:3.1.0-alpine

# Install system dependencies
RUN apk add --no-cache \
    build-base \
    postgresql-dev \
    imagemagick \
    imagemagick-dev \
    tzdata \
    git

# Set up working directory
WORKDIR /app

# Install bundler
RUN gem install bundler:2.3.0

# Copy Gemfile and Gemfile.lock
COPY Gemfile Gemfile.lock ./

# Install Ruby dependencies
RUN bundle config set --local without 'development test'
RUN bundle install --jobs 4 --retry 3

# Copy application code
COPY . .

# Precompile assets
RUN bundle exec rails assets:precompile

# Set up database
ENV RAILS_ENV=production
ENV RAILS_LOG_TO_STDOUT=true
ENV RAILS_SERVE_STATIC_FILES=true

# Expose port
EXPOSE 3000

# Start the application
CMD ["rails", "server", "-b", "0.0.0.0"]
```

### Docker Compose

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "3000:3000"
    environment:
      - RAILS_ENV=development
      - DATABASE_URL=postgresql://postgres:password@postgres:5432/myapp_development
      - REDIS_URL=redis://redis:6379/0
    volumes:
      - .:/app
      - bundle_cache:/usr/local/bundle
    depends_on:
      - postgres
      - redis
    command: bash -c "rm -f tmp/pids/server.pid && bundle exec rails server -b 0.0.0.0"

  postgres:
    image: postgres:13
    environment:
      - POSTGRES_DB=myapp_development
      - POSTGRES_USER=postgres
      - POSTGRES_PASSWORD=password
    volumes:
      - postgres_data:/var/lib/postgresql/data
    ports:
      - "5432:5432"

  redis:
    image: redis:6-alpine
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data

  sidekiq:
    build: .
    command: bundle exec sidekiq
    environment:
      - RAILS_ENV=development
      - DATABASE_URL=postgresql://postgres:password@postgres:5432/myapp_development
      - REDIS_URL=redis://redis:6379/0
    volumes:
      - .:/app
      - bundle_cache:/usr/local/bundle
    depends_on:
      - postgres
      - redis

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
    depends_on:
      - app

volumes:
  postgres_data:
  redis_data:
  bundle_cache:
```

### Multi-stage Dockerfile

```dockerfile
# Multi-stage Dockerfile for optimized production build
FROM ruby:3.1.0-alpine AS builder

# Install build dependencies
RUN apk add --no-cache \
    build-base \
    postgresql-dev \
    imagemagick-dev \
    git

WORKDIR /app

# Copy Gemfile and install dependencies
COPY Gemfile Gemfile.lock ./
RUN gem install bundler:2.3.0
RUN bundle config set --local without 'development test'
RUN bundle install --deployment --without development test

# Copy application code
COPY . .

# Precompile assets
RUN bundle exec rails assets:precompile

# Production image
FROM ruby:3.1.0-alpine AS production

# Install runtime dependencies
RUN apk add --no-cache \
    postgresql-dev \
    imagemagick \
    imagemagick-libs \
    tzdata \
    curl

# Create app user
RUN addgroup -g 1000 app && \
    adduser -D -s /bin/sh -u 1000 -G app app

WORKDIR /app

# Copy gems from builder
COPY --from=builder /usr/local/bundle/ /usr/local/bundle/
COPY --from=builder /app/Gemfile /app/Gemfile
COPY --from=builder /app/Gemfile.lock /app/Gemfile.lock

# Copy precompiled assets
COPY --from=builder /app/public/assets /app/public/assets
COPY --from=builder /app/tmp/cache/assets /app/tmp/cache/assets

# Copy application code
COPY . .

# Set permissions
RUN chown -R app:app /app

# Switch to app user
USER app

# Set environment variables
ENV RAILS_ENV=production
ENV RAILS_LOG_TO_STDOUT=true
ENV RAILS_SERVE_STATIC_FILES=true
ENV RAILS_MASTER_KEY=${RAILS_MASTER_KEY}

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
  CMD curl -f http://localhost:3000/health || exit 1

# Expose port
EXPOSE 3000

# Start the application
CMD ["rails", "server", "-b", "0.0.0.0"]
```

## Configuration Management

### Ansible Playbook

```yaml
# playbook.yml
---
- name: Deploy Ruby application
  hosts: webservers
  become: yes
  vars:
    app_name: myapp
    app_user: deploy
    app_path: /var/www/myapp
    ruby_version: 3.1.0
    
  tasks:
    - name: Update apt cache
      apt:
        update_cache: yes
      when: ansible_os_family == "Debian"
    
    - name: Install system dependencies
      package:
        name:
          - curl
          - wget
          - git
          - build-essential
          - libpq-dev
          - imagemagick
          - nodejs
          - npm
        state: present
    
    - name: Create application user
      user:
        name: "{{ app_user }}"
        shell: /bin/bash
        home: "{{ app_path }}"
        create_home: yes
        state: present
    
    - name: Create application directory
      file:
        path: "{{ app_path }}"
        state: directory
        owner: "{{ app_user }}"
        group: "{{ app_user }}"
        mode: '0755'
    
    - name: Clone application repository
      git:
        repo: 'https://github.com/yourusername/myapp.git'
        dest: "{{ app_path }}/current"
        version: main
        force: yes
      become_user: "{{ app_user }}"
    
    - name: Install Ruby dependencies
      bundler:
        state: present
        gemfile: "{{ app_path }}/current/Gemfile"
        deployment_mode: true
      become_user: "{{ app_user }}"
    
    - name: Copy environment file
      template:
        src: .env.j2
        dest: "{{ app_path }}/current/.env"
        owner: "{{ app_user }}"
        group: "{{ app_user }}"
        mode: '0600'
    
    - name: Run database migrations
      command: |
        cd {{ app_path }}/current && \
        bundle exec rails db:migrate RAILS_ENV=production
      become_user: "{{ app_user }}"
    
    - name: Precompile assets
      command: |
        cd {{ app_path }}/current && \
        bundle exec rails assets:precompile RAILS_ENV=production
      become_user: "{{ app_user }}"
    
    - name: Create systemd service
      template:
        src: myapp.service.j2
        dest: /etc/systemd/system/myapp.service
      notify: Restart myapp
    
    - name: Enable and start service
      systemd:
        name: myapp
        enabled: yes
        state: started
  
  handlers:
    - name: Restart myapp
      systemd:
        name: myapp
        state: restarted
```

### Environment Variables Template

```jinja
# .env.j2
RAILS_ENV=production
RAILS_MASTER_KEY={{ vault_rails_master_key }}
DATABASE_URL=postgresql://{{ db_user }}:{{ db_password }}@{{ db_host }}:{{ db_port }}/{{ db_name }}
REDIS_URL=redis://{{ redis_host }}:{{ redis_port }}/0
SECRET_KEY_BASE={{ vault_secret_key_base }}
SMTP_HOST={{ smtp_host }}
SMTP_PORT={{ smtp_port }}
SMTP_USER={{ smtp_user }}
SMTP_PASSWORD={{ vault_smtp_password }}
```

### Systemd Service Template

```ini
# myapp.service.j2
[Unit]
Description=MyApp Ruby Application
After=network.target

[Service]
Type=simple
User={{ app_user }}
WorkingDirectory={{ app_path }}/current
ExecStart=/usr/local/bin/bundle exec rails server -b 0.0.0.0
Restart=always
RestartSec=5
Environment=RAILS_ENV=production

[Install]
WantedBy=multi-user.target
```

## Infrastructure as Code

### Terraform Configuration

```hcl
# main.tf
provider "aws" {
  region = var.aws_region
}

# VPC
resource "aws_vpc" "main" {
  cidr_block = "10.0.0.0/16"
  enable_dns_hostnames = true
  enable_dns_support = true
  
  tags = {
    Name = "myapp-vpc"
    Environment = var.environment
  }
}

# Subnets
resource "aws_subnet" "public" {
  count = 2
  vpc_id = aws_vpc.main.id
  cidr_block = "10.0.${count.index + 1}.0.0/24"
  availability_zone = data.aws_availability_zones.available.names[count.index]
  map_public_ip_on_launch = true
  
  tags = {
    Name = "myapp-public-${count.index + 1}"
    Environment = var.environment
  }
}

resource "aws_subnet" "private" {
  count = 2
  vpc_id = aws_vpc.main.id
  cidr_block = "10.0.${count.index + 3}.0.0/24"
  availability_zone = data.aws_availability_zones.available.names[count.index]
  
  tags = {
    Name = "myapp-private-${count.index + 1}"
    Environment = var.environment
  }
}

# Security Group
resource "aws_security_group" "web" {
  name        = "myapp-web-sg"
  description = "Allow HTTP/HTTPS traffic"
  vpc_id      = aws_vpc.main.id
  
  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
  
  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
  
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
  
  tags = {
    Name = "myapp-web-sg"
    Environment = var.environment
  }
}

# Load Balancer
resource "aws_lb" "web" {
  name               = "myapp-lb"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [aws_security_group.web.id]
  subnets            = aws_subnet.public[*].id
  
  enable_deletion_protection = false
  
  tags = {
    Name = "myapp-lb"
    Environment = var.environment
  }
}

resource "aws_lb_target_group" "web" {
  name     = "myapp-tg"
  port     = 80
  protocol = "HTTP"
  vpc_id   = aws_vpc.main.id
  
  health_check {
    enabled = true
    path    = "/health"
    matcher = "200"
    interval = 30
    timeout = 5
  }
  
  tags = {
    Name = "myapp-tg"
    Environment = var.environment
  }
}

resource "aws_lb_listener" "web" {
  load_balancer_arn = aws_lb.web.arn
  port              = "80"
  protocol          = "HTTP"
  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.web.arn
  }
  
  tags = {
    Name = "myapp-listener"
    Environment = var.environment
  }
}

# ECS Cluster
resource "aws_ecs_cluster" "main" {
  name = "myapp-cluster"
  
  setting {
    name  = "containerInsights"
    value = "enabled"
  }
  
  tags = {
    Name = "myapp-cluster"
    Environment = var.environment
  }
}

# Task Definition
resource "aws_ecs_task_definition" "web" {
  family                   = "myapp"
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  cpu                      = "256"
  memory                   = "512"
  
  container_definitions = {
    web = {
      name  = "myapp"
      image = var.app_image
      cpu   = 256
      memory = 512
      
      environment = [
        {
          name  = "RAILS_ENV"
          value = "production"
        },
        {
          name  = "DATABASE_URL"
          value = var.database_url
        },
        {
          name  = "REDIS_URL"
          value = var.redis_url
        }
      ]
      
      port_mappings = [
        {
          containerPort = 3000
          protocol      = "tcp"
        }
      ]
      
      log_configuration = {
        logDriver = "awslogs"
        options = {
          awslogs-group = "/ecs/myapp"
          awslogs-region = var.aws_region
          awslogs-stream-prefix = "ecs"
        }
      }
    }
  }
  
  tags = {
    Name = "myapp-task-def"
    Environment = var.environment
  }
}

# ECS Service
resource "aws_ecs_service" "web" {
  name            = "myapp-service"
  cluster         = aws_ecs_cluster.main.id
  task_definition = aws_ecs_task_definition.web.arn
  desired_count   = 2
  launch_type     = "FARGATE"
  
  network_configuration {
    subnets = aws_subnet.private[*].id
    security_groups = [aws_security_group.web.id]
  }
  
  load_balancer {
    target_group_arn = aws_lb_target_group.web.arn
    container_name   = "myapp"
    container_port   = 3000
  }
  
  tags = {
    Name = "myapp-service"
    Environment = var.environment
  }
}
```

### Kubernetes Deployment

```yaml
# k8s/deployment.yml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: myapp
  labels:
    app: myapp
spec:
  replicas: 3
  selector:
    matchLabels:
      app: myapp
  template:
    metadata:
      labels:
        app: myapp
    spec:
      containers:
      - name: myapp
        image: yourusername/myapp:latest
        ports:
        - containerPort: 3000
        env:
        - name: RAILS_ENV
          value: "production"
        - name: DATABASE_URL
          valueFrom:
            secretKeyRef:
              name: myapp-secrets
              key: database-url
        - name: REDIS_URL
          valueFrom:
            secretKeyRef:
              name: myapp-secrets
              key: redis-url
        - name: RAILS_MASTER_KEY
          valueFrom:
            secretKeyRef:
              name: myapp-secrets
              key: rails-master-key
        resources:
          requests:
            memory: "256Mi"
            cpu: "250m"
          limits:
            memory: "512Mi"
            cpu: "500m"
        livenessProbe:
          httpGet:
            path: /health
            port: 3000
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /health
            port: 3000
          initialDelaySeconds: 5
          periodSeconds: 5
---
apiVersion: v1
kind: Service
metadata:
  name: myapp-service
spec:
  selector:
    app: myapp
  ports:
  - protocol: TCP
    port: 80
    targetPort: 3000
  type: LoadBalancer
---
apiVersion: v1
kind: Secret
metadata:
  name: myapp-secrets
type: Opaque
data:
  database-url: <base64-encoded-database-url>
  redis-url: <base64-encoded-redis-url>
  rails-master-key: <base64-encoded-master-key>
---
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: myapp-ingress
spec:
  rules:
  - host: myapp.example.com
    http:
      paths:
      - path: /
        backend:
          service:
            name: myapp-service
            port:
              number: 80
```

## Monitoring and Logging

### Prometheus Configuration

```yaml
# prometheus.yml
global:
  scrape_interval: 15s
  evaluation_interval: 15s

rule_files:
  - "rules/*.yml"

scrape_configs:
  - job_name: 'myapp'
    static_configs:
      - targets: ['localhost:3000']
    metrics_path: '/metrics'
    scrape_interval: 30s

  - job_name: 'node'
    static_configs:
      - targets: ['localhost:9100']

  - job_name: 'postgres'
    static_configs:
      - targets: ['localhost:9187']

  - job_name: 'redis'
    static_configs:
      - targets: ['localhost:9121']

alerting:
  alertmanagers:
    - static_configs:
      - targets:
        - alertmanager:9093
```

### Grafana Dashboard

```json
{
  "dashboard": {
    "title": "MyApp Monitoring",
    "panels": [
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
        ]
      },
      {
        "title": "Request Rate",
        "type": "graph",
        "targets": [
          {
            "expr": "rate(http_requests_total[5m])",
            "legendFormat": "Requests/sec"
          }
        ]
      },
      {
        "title": "Error Rate",
        "type": "graph",
        "targets": [
          {
            "expr": "rate(http_requests_total{status=~\"5..\"}[5m])",
            "legendFormat": "Errors/sec"
          }
        ]
      },
      {
        "title": "Database Connections",
        "type": "graph",
        "targets": [
          {
            "expr": "pg_stat_database_numbackends",
            "legendFormat": "Active connections"
          }
        ]
      }
    ]
  }
}
```

### Logstash Configuration

```ruby
# logstash.conf
input {
  beats {
    port => 5044
  }
}

filter {
  if [fields][service] == "myapp" {
    grok {
      match => { "message" => "%{TIMESTAMP_ISO8601:timestamp} \[%{DATA:log_level}\] %{GREEDYDATA:thread}: %{GREEDYDATA:logger}: %{GREEDYDATA:message}" }
    }
    
    date {
      match => [ "timestamp", "ISO8601" ]
    }
    
    mutate {
      convert => [ "log_level", "string" ]
    }
  }
}

output {
  elasticsearch {
    hosts => ["elasticsearch:9200"]
    index => "myapp-%{+YYYY.MM.dd}"
  }
}
```

## Automation Scripts

### Deployment Script

```ruby
#!/usr/bin/env ruby
# deploy.rb

require 'net/ssh'
require 'net/scp'
require 'yaml'

class Deployer
  def initialize(config_file)
    @config = YAML.load_file(config_file)
    @ssh = Net::SSH.start(@config['host'], @config['user'], password: @config['password'])
  end
  
  def deploy
    puts "Starting deployment..."
    
    # Backup current version
    backup_current
    
    # Pull latest code
    pull_latest
    
    # Install dependencies
    install_dependencies
    
    # Run database migrations
    run_migrations
    
    # Precompile assets
    precompile_assets
    
    # Restart application
    restart_application
    
    # Health check
    health_check
    
    puts "Deployment completed successfully!"
  rescue => e
    puts "Deployment failed: #{e.message}"
    rollback
  ensure
    @ssh.close
  end
  
  private
  
  def backup_current
    puts "Creating backup..."
    @ssh.exec!("tar -czf /var/backups/myapp-#{Time.now.strftime('%Y%m%d_%H%M%S')}.tar.gz /var/www/myapp")
  end
  
  def pull_latest
    puts "Pulling latest code..."
    @ssh.exec!("cd /var/www/myapp && git pull origin main")
  end
  
  def install_dependencies
    puts "Installing dependencies..."
    @ssh.exec!("cd /var/www/myapp && bundle install --deployment")
  end
  
  def run_migrations
    puts "Running database migrations..."
    @ssh.exec!("cd /var/www/myapp && bundle exec rails db:migrate RAILS_ENV=production")
  end
  
  def precompile_assets
    puts "Precompiling assets..."
    @ssh.exec!("cd /var/www/myapp && bundle exec rails assets:precompile RAILS_ENV=production")
  end
  
  def restart_application
    puts "Restarting application..."
    @ssh.exec!("sudo systemctl restart myapp")
    @ssh.exec!("sudo systemctl status myapp")
  end
  
  def health_check
    puts "Performing health check..."
    response = Net::HTTP.get_response(URI("http://#{@config['host']}/health"))
    if response.code.to_i == 200
      puts "Health check passed!"
    else
      raise "Health check failed: #{response.code}"
    end
  end
  
  def rollback
    puts "Rolling back..."
    @ssh.exec!("cd /var/www/myapp && git checkout HEAD~1")
    restart_application
  end
end

# Usage
deployer = Deployer.new('deploy.yml')
deployer.deploy
```

### Backup Script

```ruby
#!/usr/bin/env ruby
# backup.rb

require 'net/ssh'
require 'yaml'
require 'date'

class BackupManager
  def initialize(config_file)
    @config = YAML.load_file(config_file)
    @ssh = Net::SSH.start(@config['host'], @config['user'], password: @config['password'])
  end
  
  def create_backup
    timestamp = Time.now.strftime('%Y%m%d_%H%M%S')
    backup_name = "myapp_backup_#{timestamp}"
    
    puts "Creating backup: #{backup_name}"
    
    # Database backup
    backup_database(backup_name)
    
    # Application files backup
    backup_files(backup_name)
    
    # Upload to S3
    upload_to_s3(backup_name)
    
    # Cleanup old backups
    cleanup_old_backups
    
    puts "Backup completed: #{backup_name}"
  end
  
  private
  
  def backup_database(backup_name)
    puts "Creating database backup..."
    @ssh.exec!("pg_dump #{@config['database_name']} > /tmp/#{backup_name}_db.sql")
  end
  
  def backup_files(backup_name)
    puts "Creating files backup..."
    @ssh.exec!("tar -czf /tmp/#{backup_name}_files.tar.gz /var/www/myapp")
  end
  
  def upload_to_s3(backup_name)
    puts "Uploading to S3..."
    @ssh.exec!("aws s3 cp /tmp/#{backup_name}_db.sql s3://#{@config['s3_bucket']}/backups/")
    @ssh.exec!("aws s3 cp /tmp/#{backup_name}_files.tar.gz s3://#{@config['s3_bucket']}/backups/")
  end
  
  def cleanup_old_backups
    puts "Cleaning up old backups..."
    @ssh.exec!("aws s3 ls s3://#{@config['s3_bucket']}/backups/ | awk '$NF > 7 {print $NF}' | sort -r | tail -n +6 | xargs -I {} aws s3 rm s3://#{@config['s3_bucket']}/backups/{}")
  end
  
  def restore_backup(backup_name)
    puts "Restoring backup: #{backup_name}"
    
    # Download from S3
    @ssh.exec!("aws s3 cp s3://#{@config['s3_bucket']}/backups/#{backup_name}_db.sql /tmp/")
    @ssh.exec!("aws s3 cp s3://#{@config['s3_bucket']}/backups/#{backup_name}_files.tar.gz /tmp/")
    
    # Extract files
    @ssh.exec!("cd /var/www && tar -xzf /tmp/#{backup_name}_files.tar.gz")
    
    # Restore database
    @ssh.exec!("psql #{@config['database_name']} < /tmp/#{backup_name}_db.sql")
    
    puts "Backup restored successfully!"
  end
end

# Usage
backup_manager = BackupManager.new('backup.yml')
backup_manager.create_backup
```

## Best Practices

### 1. Infrastructure as Code

```ruby
# Use version control for all infrastructure
# Document all configurations
# Use templates for consistency
# Automate everything possible
```

### 2. Security

```ruby
# Use secrets management
# Implement least privilege access
# Regular security audits
# Network segmentation
```

### 3. Monitoring

```ruby
# Monitor everything that matters
# Set up alerts for critical metrics
# Log everything
# Use structured logging
```

### 4. Disaster Recovery

```ruby
# Regular backups
# Test restore procedures
# Documentation for recovery
# Multiple environment support
```

## Practice Exercises

### Exercise 1: CI/CD Pipeline
Create a complete CI/CD pipeline with:
- Automated testing
- Security scanning
- Docker building
- Multi-environment deployment
- Rollback capabilities

### Exercise 2: Infrastructure Automation
Build infrastructure automation with:
- Terraform templates
- Ansible playbooks
- Kubernetes manifests
- Configuration management
- Monitoring setup

### Exercise 3: Monitoring System
Implement a monitoring system with:
- Prometheus metrics collection
- Grafana dashboards
- Alert management
- Log aggregation
- Performance monitoring

### Exercise 4: Backup and Recovery
Create a backup and recovery system with:
- Automated backups
- Database backups
- File backups
- Cloud storage
- Restore procedures

---

**Ready to explore more advanced Ruby topics? Let's continue! 🚀**
